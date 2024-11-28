<?php

class Template {

    static $blocks = []; // Menyimpan blok section
    static $cache_path = 'cache/';
    static $cache_enabled = false;
    static $extension = '.blade.php';

    // Fungsi untuk menampilkan view
    static function view($file, $data = []) {
        $cached_file = self::cache($file);
        extract($data, EXTR_SKIP);
        require $cached_file;
    }

    // Fungsi untuk mem-cache view
    static function cache($file) {
        if (!file_exists(self::$cache_path)) {
            mkdir(self::$cache_path, 0744);
        }
        $cached_file = self::$cache_path . str_replace(['/', '.html'], ['_', ''], $file . '.php');
        
        // Jika caching tidak aktif atau file perlu di-recompile
        if (!self::$cache_enabled || !file_exists($cached_file) || filemtime($cached_file) < filemtime($file)) {
            $code = self::includeFiles($file);
            $code = self::compileCode($code);
            file_put_contents($cached_file, '<?php class_exists(\'' . __CLASS__ . '\') or exit; ?>' . PHP_EOL . $code);
        }
        
        return $cached_file;
    }

    // Fungsi untuk menghapus cache
    static function clearCache() {
        foreach (glob(self::$cache_path . '*') as $file) {
            unlink($file);
        }
    }

    // Fungsi untuk meng-compile kode
    static function compileCode($code) {
        $code = self::compileSection($code);  // Compile single-line @section
        $code = self::compileBlock($code);    // Compile @section blocks
        $code = self::compileYield($code);    // Compile @yield statements
        $code = self::compileEscapedEchos($code);
        $code = self::compileEchos($code);
        $code = self::compilePHP($code);
        return $code;
    }

    // Fungsi untuk menangani @include dan @extends
    static function includeFiles($file) {
        $file = $file . self::$extension;
        $code = file_get_contents($file);

        preg_match_all('/@(?:extends|include)\([\'"]?(.*?)?[\'"]?\)/i', $code, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $code = str_replace($match[0], self::includeFiles($match[1]), $code);
        }

        $code = preg_replace('/@(?:extends|include)\([\'"]?(.*?)?[\'"]?\)/i', '', $code);
        return $code;
    }

    // Compile PHP code
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

    // Compile @section blocks
    static function compileBlock($code) {
        preg_match_all('/@section\((.*?)\)(.*?)@endsection/is', $code, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $blockName = $match[1];
            $blockContent = $match[2];
            self::$blocks[$blockName] = $blockContent; // Store block content
            $code = str_replace($match[0], '', $code); // Remove the section
        }
        
        return $code;
    }

    // Compile @yield statements
    static function compileYield($code) {
        foreach (self::$blocks as $blockName => $blockContent) {
            $code = preg_replace('/@yield\([\'"]?' . preg_quote($blockName, '/') . '[\'"]?\)/', $blockContent, $code);
        }
        
        $code = preg_replace('/@yield\([\'"]?(.*?)?[\'"]?\)/', '', $code); // Remove any other @yield
        return $code;
    }

    // Handle single-line sections like @section('name', 'value')
    static function compileSection($code) {
        // Match and process single-line @section('name', 'value')
        $code = preg_replace_callback('/@section\([\'"]?(.*?)?[\'"]?,\s*([\'"]?)(.*?)\2\s*\)/', function($matches) {
            self::$blocks[$matches[1]] = $matches[3]; // Store the section content in the blocks array
            return '';  // Remove the section from the code
        }, $code);

        return $code;
    }
}
?>
