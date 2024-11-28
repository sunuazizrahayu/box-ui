<?php
class Template {

    static $blocks = [];
    static $cache_path = 'cache/';
    static $cache_enabled = false;
    static $extension = '.blade.php';

    // Main method for rendering views
    static function view($file, $data = []) {
        $cached_file = self::cache($file);
        extract($data, EXTR_SKIP);
        require $cached_file;
    }

    // Caching the compiled version of the view
    static function cache($file) {
        if (!file_exists(self::$cache_path)) {
            mkdir(self::$cache_path, 0744);
        }
        $cached_file = self::$cache_path . str_replace(['/', '.html'], ['_', ''], $file . '.php');
        
        // Check if the cache is enabled and the file needs to be recompiled
        if (!self::$cache_enabled || !file_exists($cached_file) || filemtime($cached_file) < filemtime($file)) {
            $code = self::includeFiles($file);
            $code = self::compileCode($code);
            file_put_contents($cached_file, '<?php class_exists(\'' . __CLASS__ . '\') or exit; ?>' . PHP_EOL . $code);
        }
        
        return $cached_file;
    }

    // Clear the compiled cache files
    static function clearCache() {
        foreach (glob(self::$cache_path . '*') as $file) {
            unlink($file);
        }
    }

    // Compile the view code (blocks, yields, echo statements, etc.)
    static function compileCode($code) {
        $code = self::compileBlock($code);
        $code = self::compileYield($code);
        $code = self::compileEscapedEchos($code);
        $code = self::compileEchos($code);
        $code = self::compilePHP($code);
        return $code;
    }

    // Handling includes and extends in Blade-like syntax
    static function includeFiles($file) {
        $file = $file . self::$extension;
        $code = file_get_contents($file);

        preg_match_all('/@(?:extends|include)\([\'"]?(.*?)?[\'"]?\)/i', $code, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $code = str_replace($match[0], self::includeFiles($match[1]), $code);
        }

        // Remove the includes/extends after processing
        $code = preg_replace('/@(?:extends|include)\([\'"]?(.*?)?[\'"]?\)/i', '', $code);
        return $code;
    }

    // Convert PHP code
    static function compilePHP($code) {
        return preg_replace('~@php\s*(.+?)\s*@endphp~is', '<?php $1 ?>', $code);
    }

    // Compile echo statements
    static function compileEchos($code) {
        return preg_replace('~\{{\s*(.+?)\s*\}}~is', '<?php echo $1 ?>', $code);
    }

    // Compile escaped echo statements
    static function compileEscapedEchos($code) {
        return preg_replace('~\{{{\s*(.+?)\s*\}}}~is', '<?php echo htmlentities($1, ENT_QUOTES, \'UTF-8\') ?>', $code);
    }

    // Handle block definitions (@section)
    static function compileBlock($code) {
        preg_match_all('/@section\((.*?)\)(.*?)@endsection/is', $code, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $blockName = $match[1];
            $blockContent = $match[2];
            self::$blocks[$blockName] = $blockContent;
            $code = str_replace($match[0], '', $code);
        }

        return $code;
    }

    // Handle yielding content (@yield)
    static function compileYield($code) {
        foreach (self::$blocks as $blockName => $blockContent) {
            $code = preg_replace('/@yield\(' . preg_quote($blockName) . '\)/', $blockContent, $code);
        }
        
        // Remove any other @yield that doesn't match a block
        $code = preg_replace('/@yield\([\'"]?(.*?)?[\'"]?\)/', '', $code);
        
        return $code;
    }

    // Handle parent content (@parent)
    static function compileParent($code) {
        return preg_replace_callback('/@parent/', function () {
            return self::$blocks['parent'] ?? '';
        }, $code);
    }

}

?>
