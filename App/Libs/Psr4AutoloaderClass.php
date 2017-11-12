<?php
/**
 * Created by PhpStorm.
 * User: crazytang
 * Date: 16/3/25
 * Time: 01:32
 */

namespace App;


/**
 * An example of a general-purpose implementation that includes the optional
 * functionality of allowing multiple base directories for a single namespace
 * prefix.
 * 下面例子中在一个命名空间前缀下有多个base目录。
 *
 * Given a foo-bar package of classes in the file system at the following
 * paths ...
 * 在下面路径中foo-bar包中存在以下类：
 *
 *     /path/to/packages/foo-bar/
 *         src/
 *             Baz.php             # Foo\Bar\Baz
 *             Qux/
 *                 Quux.php        # Foo\Bar\Qux\Quux
 *         tests/
 *             BazTest.php         # Foo\Bar\BazTest
 *             Qux/
 *                 QuuxTest.php    # Foo\Bar\Qux\QuuxTest
 *
 * ... add the path to the class files for the \Foo\Bar\ namespace prefix
 * as follows:
 * ...对\Foo\Bar\命名空间前缀，添加类文件的路径
 *
 *      <?php
 *      // instantiate the loader
 *      // 初始化loader
 *      $loader = new \Example\Psr4AutoloaderClass;
 *
 *      // register the autoloader
 *      // 注册autoloader
 *      $loader->register();
 *
 *      // register the base directories for the namespace prefix
 *      // 注册命名空间前缀的多个base目录
 *      $loader->addNamespace('Foo\Bar', '/path/to/packages/foo-bar/src');
 *      $loader->addNamespace('Foo\Bar', '/path/to/packages/foo-bar/tests');
 *
 * The following line would cause the autoloader to attempt to load the
 * \Foo\Bar\Qux\Quux class from /path/to/packages/foo-bar/src/Qux/Quux.php:
 * 下面代码将用/path/to/packages/foo-bar/src/Qux/Quux.php文件来加载\Foo\Bar\Qux\Quux类。
 *
 *      <?php
 *      new \Foo\Bar\Qux\Quux;
 *
 * The following line would cause the autoloader to attempt to load the
 * \Foo\Bar\Qux\QuuxTest class from /path/to/packages/foo-bar/tests/Qux/QuuxTest.php:
 * 下面代码将用/path/to/packages/foo-bar/tests/Qux/QuuxTest.php文件来加载
 * \Foo\Bar\Qux\QuuxTest类。
 *
 *      <?php
 *      new \Foo\Bar\Qux\QuuxTest;
 */
class Psr4AutoloaderClass
{
    /**
     * An associative array where the key is a namespace prefix and the value
     * is an array of base directories for classes in that namespace.
     * 定义一个数组：key为命名空间前缀，value为一个数组，每一项表示命名空间中类对应的base目录.
     *
     * @var array
     */
    protected $prefixes = array();

    /**
     * Register loader with SPL autoloader stack.
     * 利用SPL自动加载器来注册loader
     *
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * Adds a base directory for a namespace prefix.
     * 为一个命名空间前缀添加对应的base目录
     *
     * @param string $prefix The namespace prefix.
     * @param string $base_dir A base directory for class files in the
     * namespace.
     * @param bool $prepend If true, prepend the base directory to the stack
     * instead of appending it; this causes it to be searched first rather
     * than last.
     * @return void
     */
    public function addNamespace($prefix, $base_dir, $prepend = false)
    {
        // normalize namespace prefix
        // 规范命名空间前缀
        $prefix = trim($prefix, '\\') . '\\';

        // normalize the base directory with a trailing separator
        // 用'/'字符来规范base目录
        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';

        // initialize the namespace prefix array
        // 初始化命名空间前缀数组
        if (isset($this->prefixes[$prefix]) === false) {
            $this->prefixes[$prefix] = array();
        }

        // retain the base directory for the namespace prefix
        // 绑定命名空间前缀对应的base目录
        if ($prepend) {
            array_unshift($this->prefixes[$prefix], $base_dir);
        } else {
            array_push($this->prefixes[$prefix], $base_dir);
        }
    }

    /**
     * Loads the class file for a given class name.
     * 根据类名来加载类文件。
     *
     * @param string $class The fully-qualified class name.
     * @return mixed The mapped file name on success, or boolean false on
     * failure.
     */
    public function loadClass($class)
    {
        // the current namespace prefix
        $prefix = $class;

        // work backwards through the namespace names of the fully-qualified
        // class name to find a mapped file name
        // 从后面开始遍历完全合格类名中的命名空间名称, 来查找映射的文件名
        while (false !== $pos = strrpos($prefix, '\\')) {

            // retain the trailing namespace separator in the prefix
            // 保留命名空间前缀中尾部的分隔符
            $prefix = substr($class, 0, $pos + 1);

            // the rest is the relative class name
            // 剩余的就是相对类名称
            $relative_class = substr($class, $pos + 1);

            // try to load a mapped file for the prefix and relative class
            // 利用命名空间前缀和相对类名来加载映射文件
            $mapped_file = $this->loadMappedFile($prefix, $relative_class);
            if ($mapped_file) {
                return $mapped_file;
            }

            // remove the trailing namespace separator for the next iteration
            // of strrpos()
            // 删除命名空间前缀尾部的分隔符，以便用于下一次strrpos()迭代
            $prefix = rtrim($prefix, '\\');
        }

        // never found a mapped file
        // 未找到映射文件
        return false;
    }

    /**
     * Load the mapped file for a namespace prefix and relative class.
     * 根据命名空间前缀和相对类来加载映射文件
     *
     * @param string $prefix The namespace prefix.
     * @param string $relative_class The relative class name.
     * @return mixed Boolean false if no mapped file can be loaded, or the
     * name of the mapped file that was loaded.
     */
    protected function loadMappedFile($prefix, $relative_class)
    {
        // are there any base directories for this namespace prefix?
        // 命名空间前缀中有base目录吗？
        if (isset($this->prefixes[$prefix]) === false) {
            return false;
        }

        // look through base directories for this namespace prefix
        // 遍历命名空间前缀的base目录
        foreach ($this->prefixes[$prefix] as $base_dir) {

            // replace the namespace prefix with the base directory,
            // replace namespace separators with directory separators
            // in the relative class name, append with .php
            // 用base目录替代命名空间前缀,
            // 在相对类名中用目录分隔符'/'来替换命名空间分隔符'\',
            // 并在后面追加.php组成$file的绝对路径
            $file = $base_dir
                . str_replace('\\', '/', $relative_class)
                . '.php';

            // if the mapped file exists, require it
            // 若映射文件存在，则require该文件
            if ($this->requireFile($file)) {
                // yes, we're done
                return $file;
            }
        }

        // never found it
        return false;
    }

    /**
     * If a file exists, require it from the file system.
     *
     * @param string $file The file to require.
     * @return bool True if the file exists, false if not.
     */
    protected function requireFile($file)
    {
        if (file_exists($file)) {
            require $file;
            return true;
        }
        return false;
    }
}