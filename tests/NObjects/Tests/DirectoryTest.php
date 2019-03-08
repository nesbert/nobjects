<?php
namespace NObjects\Tests;

use NObjects\Directory;

class DirectoryTest extends \PHPUnit_Framework_TestCase
{

    private $dir;
    private $subdir;
    private $files = array();
    private $subfiles = array();

    public function setUp()
    {
        parent::setUp();

        $this->dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test';
        $this->subdir = $this->dir . DIRECTORY_SEPARATOR . 'sub';

        $this->files[] = $this->dir . DIRECTORY_SEPARATOR . '.test-hidden';
        $this->files[] = $this->dir . DIRECTORY_SEPARATOR . 'file1.php';
        $this->files[] = $this->dir . DIRECTORY_SEPARATOR . 'file2.php';
        $this->files[] = $this->dir . DIRECTORY_SEPARATOR . 'file3.txt';
        $this->files[] = $this->dir . DIRECTORY_SEPARATOR . 'file4.txt';

        $this->subfiles[] = $this->subdir . DIRECTORY_SEPARATOR . '.test-subhidden';
        $this->subfiles[] = $this->subdir . DIRECTORY_SEPARATOR . 'file5.php';
        $this->subfiles[] = $this->subdir . DIRECTORY_SEPARATOR . 'file6.txt';

        if (!file_exists($this->dir)) {
            mkdir($this->dir);
            mkdir($this->subdir);
        }

        // create temp files
        array_map('touch', $this->files);
        array_map('touch', $this->subfiles);
    }

    public function tearDown()
    {
        if (file_exists($this->dir)) {
            array_map('unlink', $this->subfiles);
            array_map('unlink', $this->files);
            rmdir($this->subdir);
            rmdir($this->dir);
        }

        parent::tearDown();
    }

    public function testLs()
    {
        $ls = Directory::ls($this->dir);

        foreach ($this->files as $file) {
            if (strstr($file, 'hidden')) {
                continue;
            }
            $this->assertTrue(array_search($file, $ls) !== false);
        }

        $ls = Directory::ls($this->subdir);

        foreach ($this->subfiles as $file) {
            if (strstr($file, 'hidden')) {
                continue;
            }
            $this->assertTrue(array_search($file, $ls) !== false);
        }
    }

    public function testLsOptions()
    {
        $current = $this->subdir;
        $root = $this->dir;

        $ls = Directory::ls($current);
        $this->assertEquals(2, count($ls));

        $ls = Directory::ls($root);
        $this->assertEquals(4, count($ls));

        $opts = array('showDirs' => true);
        $ls = Directory::ls($root, $opts);
        $this->assertEquals(5, count($ls));


        $opts['showDirs'] = false;
        $opts['recursive'] = true;
        $opts['filter'] = '/.php$/';
        $ls = Directory::ls($root, $opts);
        $this->assertEquals(3, count($ls));

        $opts['group'] = true;
        $ls = Directory::ls($root, $opts);

        $this->assertEquals(2, count($ls));
        $this->assertEquals(1, count($ls[$current]));
        $this->assertEquals(2, count($ls[$root]));

        $opts['showDirs'] = true;
        $ls = Directory::ls($root, $opts);
        $this->assertEquals(2, count($ls));
        $this->assertEquals(1, count($ls[$current]));
        $this->assertEquals(3, count($ls[$root]));

        $ls = Directory::ls($root, array('recursive' => true, 'showInvisible' => true));
        $this->assertEquals(8, count($ls));
        $this->assertTrue(in_array($this->files[0], $ls));
        $this->assertTrue(in_array($this->subfiles[0], $ls));
    }

    public function testLsWithFilename()
    {
        $ls = Directory::lsWithFilename($this->dir);

        $this->assertFalse(in_array($this->files[0], $ls));
        $this->assertTrue(in_array($this->files[1], $ls));
        $this->assertTrue(in_array($this->files[2], $ls));
        $this->assertFalse(in_array($this->files[3], $ls));
        $this->assertFalse(in_array($this->files[4], $ls));

        $ls = Directory::lsWithFilename($this->dir, 'txt');
        $this->assertEquals(2, count($ls));
        $this->assertEquals($this->files[3], current($ls));
    }
}
