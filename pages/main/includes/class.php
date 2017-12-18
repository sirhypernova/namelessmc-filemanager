<?php
/*
 *	Made by SirHyperNova
 *  NamelessMC version 2.0.0-pr3
 *
 *  License: MIT
 *
 *  File Manager Module
 */
class Files {
    /**
     * Base Directory to display files from
     */
	protected static $basedir;
	/**
	 * Option to prevent viewing of parent directory
	 */
	protected static $backlog;
	/**
	 * Allowed File Extentions
	 */
	protected static $alcontent;
	/**
	 * Dissallowed File Extentions (for editing)
	 */
	protected static $cnview;
	/**
	 * Regex to cleanse file names
	 */
	protected static $rename;
	/**
	 * Regular Expression to detect URLs
	 */
	public static $regurl;
	/**
	 * Maximum file size, in bytes
	 */
	protected static $maxsize;
	function __construct ($backlog = false, $basedir = __DIR__, $alcontent = ['js', 'css', 'txt', 'doc', 'docx', 'pdf', 'jpg', 'jpeg', 'png', 'gif'], $maxsize = 2097152) {
		self::$backlog = $backlog;
		self::$basedir = $basedir;
		self::$alcontent = $alcontent;
		self::$maxsize = $maxsize;
		self::$cnview = ['png','jpg','jpeg','zip','gz','doc','docx','gif','pdf'];
		self::$rename = "/[^0-9a-zA-Z\s.\-()]/m";
		self::$regurl = '_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]-*)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]-*)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,}))\.?)(?::\d{2,5})?(?:[/?#]\S*)?$_iuS';
		ini_set('upload_max_filesize',$maxsize);
		ini_set('post_max_size',$maxsize);
	}
	/**
	 * Get a File
	 */
    public function get($fname = null) {
        $basedir = Files::$basedir.'/';
        if (!file_exists($basedir)) {
            mkdir($basedir);
        }
        if ($fname == null) {
            return false;
        } else {
            if (Files::$backlog == false) {
                if (0 === strpos(realpath($basedir.'/'.$fname),$basedir)) {
                    $fpdir = $basedir.'/'.$fname;
                    if (file_exists($fpdir)) {
                        if (in_array(pathinfo($fpdir,PATHINFO_EXTENSION),Files::$alcontent)) {
                            return new File($fpdir);
                        } elseif (is_dir($fpdir)) {
                            return new Folder($fpdir);
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                $fpdir = $basedir.'/'.$fname;
                if (file_exists($fpdir)) {
                    if (in_array(pathinfo($fpdir,PATHINFO_EXTENSION),Files::$alcontent)) {
                        return new File($fpdir);
                    } elseif (is_dir($fpdir)) {
                        return new Folder($fpdir);
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }
        
        
    }
    /**
     * Create a new File
     */
    public function create ($filename, $type = "file", $dir = null, $content = null) {
        $basedir = Files::$basedir.'/';
        $filename = preg_replace(Files::$rename," ",$filename);
        $fext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!in_array($fext, Files::$alcontent) && $type != "dir") {
            return false;
        }
        if (!file_exists($basedir)) {
            mkdir($basedir);
        }
        if (strlen($content) > Files::$maxsize) {
            return false;
        }
        if ($dir == null) {
            if (file_exists($basedir.'/'.$filename) && file_exists($basedir.'/'.$dir.'/')) {
                return false;
            } else {
                if ($type == "file") {
                    if ($content == null) {
                        file_put_contents($basedir.'/'.$filename, "");
                        return new File($basedir.'/'.$filename);
                    } else {
                        file_put_contents($basedir.'/'.$filename, $content);
                        return new File($basedir.'/'.$filename);
                    }
                } elseif ($type == "dir" || $type == "directory" || $type == "folder") {
                    mkdir($basedir.'/'.$filename);
                    return new Folder($basedir.'/'.$filename);
                }
            }
        } else {
            if (Files::$backlog == false) {
                if (0 === strpos(realpath($basedir.'/'.$dir),$basedir)) {
                    if (file_exists($basedir.'/'.$dir.'/'.$filename) && file_exists($basedir.'/'.$dir.'/')) {
                        return false;
                    } else {
                        if ($type == "file") {
                            if ($content == null) {
                                file_put_contents($basedir.'/'.$dir.'/'.$filename, "");
                                return new File($basedir.'/'.$dir.'/'.$filename);
                            } else {
                                file_put_contents($basedir.'/'.$dir.'/'.$filename, $content);
                                return new File($basedir.'/'.$dir.'/'.$filename);
                            }
                        } elseif ($type == "dir" || $type == "directory" || $type == "folder") {
                            mkdir($basedir.'/'.$dir.'/'.$filename);
                            return new Folder($basedir.'/'.$dir.'/'.$filename);
                        }
                    }
                }
            } else {
                if (file_exists($basedir.'/'.$dir.'/'.$filename) && file_exists($basedir.'/'.$dir.'/')) {
                    return false;
                } else {
                    if ($type == "file") {
                        if ($content == null) {
                            file_put_contents($basedir.'/'.$dir.'/'.$filename, "");
                            return new File($basedir.'/'.$dir.'/'.$filename);
                        } else {
                            file_put_contents($basedir.'/'.$dir.'/'.$filename, $content);
                            return new File($basedir.'/'.$dir.'/'.$filename);
                        }
                    } elseif ($type == "dir" || $type == "directory" || $type == "folder") {
                        mkdir($basedir.'/'.$dir.'/'.$filename);
                        return new Folder($basedir.'/'.$dir.'/'.$filename);
                    }
                }
            }
        }
        
    }
    /**
     * Upload a file
     */
    public function upload ($file, $dir = null) {
        $basedir = Files::$basedir.'/';
        if (isset($file)) {
            $filename = preg_replace(Files::$rename," ",$file["name"]);
            if ($dir == null) {
                $canupload = 1;
                $fileType = pathinfo($filename, PATHINFO_EXTENSION);
                if ($file["size"] > Files::$maxsize) {
                    return false;
                }
                if (!in_array($fileType, Files::$alcontent)) {
                    return false;
                }
                if (file_exists($tfile)) {
                    return false;
                }
                if (move_uploaded_file($file["tmp_name"], "$basedir/$filename")) {
                    if (is_dir("$basedir/$dir/$filename")) {
                        return new Folder("$basedir/$dir/$filename");
                    } else {
                        return new File("$basedir/$dir/$filename");
                    }
                } else {
                    return false;
                }
            } else {
                if (Files::$backlog == false) {
                    if (0 === strpos(realpath($basedir.'/'.$dir),$basedir)) {
                        $canupload = 1;
                        $fileType = pathinfo($file["name"], PATHINFO_EXTENSION);
                        if ($file["size"] > 2 * 1024 * 1024) {
                            return false;
                        }
                        if (!in_array($fileType, Files::$alcontent)) {
                            return false;
                        }
                        if (file_exists($basedir.'/'.$dir.'/'.$file["name"])) {
                            return false;
                        }
                        if (move_uploaded_file($file["tmp_name"], "$basedir/$dir/$filename")) {
                            if (is_dir("$basedir/$dir/$filename")) {
                                return new Folder("$basedir/$dir/$filename");
                            } else {
                                return new File("$basedir/$dir/$filename");
                            }
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                } else {
                    $canupload = 1;
                    $fileType = pathinfo($file["name"], PATHINFO_EXTENSION);
                    if ($file["size"] > 2 * 1024 * 1024) {
                        return false;
                    }
                    if (!in_array($fileType, Files::$alcontent)) {
                        return false;
                    }
                    if (file_exists($basedir.'/'.$dir.'/'.$file["name"])) {
                        return false;
                    }
                    if (move_uploaded_file($file["tmp_name"], "$basedir/$dir/$filename")) {
                        if (is_dir("$basedir/$dir/$filename")) {
                            return new Folder("$basedir/$dir/$filename");
                        } else {
                            return new File("$basedir/$dir/$filename");
                        }
                    } else {
                        return false;
                    }
                }
            }
        } else {
            return false;
        }
    }
    /**
     * Get an array of all files and folders
     */
	public function getAll($dir = null) {
        $basedir = Files::$basedir.'/';
        if (!file_exists($basedir)) {
            mkdir($basedir);
        }
        if ($dir == null) {
            $files = array_diff(scandir($basedir), ['.', '..']);
            $allfiles = [];
            foreach ($files as $file) {
                if (in_array(pathinfo($file,PATHINFO_EXTENSION),Files::$alcontent) || is_dir($basedir.'/'.$file)) {
                    if (is_dir($basedir.'/'.$file)) {
                        $fpdir = realpath($basedir.'/'.$file);
                        $allfiles['folders'][] = new Folder($fpdir);
                    } else {
                        $fpdir = realpath($basedir.'/'.$file);
                        $allfiles['files'][] = new File($fpdir);
                    }
                }
            }
            return $allfiles;
        } else {
        	if (Files::$backlog == false) {
	            if (0 === strpos(realpath($basedir.'/'.$dir),$basedir)) {
	                $files = array_diff(scandir($basedir.'/'.$dir), ['.','..']);
	                $allfiles = [];
	                $allfiles['folders'] = [];
	                $allfiles['files'] = [];
	                foreach ($files as $file) {
	                    if (in_array(pathinfo($file,PATHINFO_EXTENSION),Files::$alcontent) || is_dir($basedir.'/'.$dir.'/'.$file)) {
    	                    if (is_dir($basedir.'/'.$dir.'/'.$file)) {
    	                        $fpdir = realpath($basedir.'/'.$dir.'/'.$file);
    	                        $allfiles['folders'][] = new Folder($fpdir);
    	                    } else {
    	                        $fpdir = realpath($basedir.'/'.$dir.'/'.$file);
    	                        $allfiles['files'][] = new File($fpdir);
    	                    }
	                    }
	                }
	                return $allfiles;   
	            } else {
	                return false;
	            }
        	} else {
        		$files = array_diff(scandir($basedir.'/'.$dir), ['.','..']);
                $allfiles = [];
                $allfiles['folders'] = [];
                $allfiles['files'] = [];
                foreach ($files as $file) {
                    if (in_array(pathinfo($file,PATHINFO_EXTENSION),Files::$alcontent) || is_dir($basedir.'/'.$dir.'/'.$file)) {
                        if (is_dir($basedir.'/'.$dir.'/'.$file)) {
                            $fpdir = realpath($basedir.'/'.$dir.'/'.$file);
                            $allfiles['folders'][] = new Folder($fpdir);
                        } else {
                            $fpdir = realpath($basedir.'/'.$dir.'/'.$file);
                            $allfiles['files'][] = new File($fpdir);
                        }
                    }
                }
                return $allfiles;  
        	}
        }
        
        
    }
}
/**
 * File class, for individual files
 */
class File extends Files {
    private $pathinfo;
    function __construct ($fpdir) {
        $pathinfo = pathinfo($fpdir);
        /**
         * File Extension
         */
        $this->ext = $pathinfo['extension'];
        /**
         * Ediing Allowed
         */
         $this->editable = (in_array($this->ext,Files::$cnview)?false:true);
        /**
         * File size in bytes
         */
        $this->sizeb = filesize($fpdir);
        if ($this->sizeb >= 1024) {
            /**
             * File size in kilobytes
             */
            $this->sizekb = round(filesize($fpdir) / 1024, 3);
        }
        if ($this->sizeb >= 1024*1024) {
            /**
             * File size in megabytes
             */
            $this->sizemb = round(filesize($fpdir) / 1024 / 1024, 3);
        }
        /**
         * Full name of file
         */
        $this->name = $pathinfo['filename'];
        $this->fullname = $pathinfo['basename'];
        $this->fullpath = realpath($fpdir);
    }
    function __toString() {
        return $this->fullname;
    }
    /**
     * Delete file
     */
    public function delete ($over = false) {
        if (file_exists($this->fullpath)) {
            if (!in_array($this->ext, Files::$alcontent) && $over == false) {
                return false;
            }
            if (unlink($this->fullpath)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    /**
     * Get file data
     */
    public function data () {
        if (file_exists($this->fullpath)) {
                return file_get_contents($this->fullpath);
        } else {
            return false;
        }
    }
    /**
     * Set file contents
     */
    public function edit ($content = null,$name = null) {
        $basedir = realpath(Files::$basedir.'/');
        if (isset($name) && in_array(pathinfo($name,PATHINFO_EXTENSION),Files::$alcontent)) {
            $sname = preg_replace(Files::$rename," ",$name);
            $dir = new Folder(str_replace($this->fullname,'',$this->fullpath));
            $name = str_replace($this->fullname,'',$this->fullpath.$sname);
        } else {
            $name = realpath($this->fullpath);
        }
        if ($this->type == "dir") {
            return false;
        }
        if (!file_exists($basedir)) {
            mkdir($basedir);
        }
        if (strlen($content) > Files::$maxsize) {
            return false;
        }
        if (0 === strpos($name,$basedir)) {
            if (isset($dir)) {
                if ($content == null) {
                    $nfile = $dir->create($sname,'file',null,$this->data());
                    $this->delete();
                    return $nfile;
                } else {
                    $this->delete();
                    return $dir->create($sname,'file',null,$content);
                }
            } else {
                if ($content == null) {
                    file_put_contents($name, "");
                    return new File(realpath($name));
                } else {
                    file_put_contents($name, $content);
                    return new File(realpath($name));
                }
            }
        }
        
    }
    public function unzip () {
        $zip = new ZipArchive;
        if ($file-ext != 'zip') return;
        $pos = strpos($this->fullpath,$this->fullname);
        if ($pos !== false) {
            $destination = realpath(substr_replace($this->fullpath,'',$pos,strlen($this->fullname))).'/'.$this->name;
        }
        $zip->open($this->fullpath);
        if (file_exists($destination)) {
            $zip->extractTo($destination);
        } else {
            mkdir($destination);
            $zip->extractTo($destination);
        }
        $dir = new RecursiveDirectoryIterator($destination);
        $dirIterator = new RecursiveIteratorIterator($dir);
        foreach ($dirIterator as $file) {
            if ($file->isFile() && !$file->isDir()) {
                $nfile = new File ($file->getPathname());
                if (!in_array($nfile->ext,Files::$alcontent)) {
                    $nfile->delete(true);
                }
            }
        }
        $zip->close();
    }
}
class Folder extends Files {
    private $pathinfo;
    function __construct ($fpdir) {
        $pathinfo = pathinfo($fpdir);
        $this->name = $pathinfo['basename'];
        $this->fullpath = realpath($fpdir);
        $this->editable = false;
    }
    function __toString() {
        return $this->name;
    }
    /**
	 * Get a File
	 */
    public function get($fname = null) {
        $basedir = $this->fullpath;
        if (!file_exists($basedir)) {
            mkdir($basedir);
        }
        if ($fname == null) {
            return false;
        } else {
            if (Files::$backlog == false) {
                if (0 === strpos(realpath($basedir.'/'.$fname),$basedir)) {
                    $fpdir = $basedir.'/'.$fname;
                    if (file_exists($fpdir)) {
                        if (in_array(pathinfo($fpdir,PATHINFO_EXTENSION),Files::$alcontent)) {
                            return new File($fpdir);
                        } elseif (is_dir($fpdir)) {
                            return new Folder($fpdir);
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                $fpdir = $basedir.'/'.$fname;
                if (file_exists($fpdir)) {
                    if (in_array(pathinfo($fpdir,PATHINFO_EXTENSION),Files::$alcontent)) {
                        return new File($fpdir);
                    } elseif (is_dir($fpdir)) {
                        return new Folder($fpdir);
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }
        
        
    }
    /**
     * Rename current folder
     */
    public function rename ($name) {
        if (isset($name)) {
            $sname = preg_replace("/[^0-9a-zA-Z\s.\-:()]/m","",$name);
            $name = str_replace($this->name,'',$this->fullpath).$sname;
        } else {
            $name = realpath($this->fullpath);
        }
        rename($this->fullpath,$name);
        return new Folder ($name);
    }
    /**
     * Get an array of all files and folders
     */
    public function getAll($dir = null) {
        $basedir = $this->fullpath;
        if (!file_exists($basedir)) {
            mkdir($basedir);
        }
        if ($dir == null) {
            $files = array_diff(scandir($basedir), ['.', '..']);
            $allfiles = [];
            foreach ($files as $file) {
                if (in_array(pathinfo($file,PATHINFO_EXTENSION),Files::$alcontent) || is_dir($basedir.'/'.$file)) {
                    if (is_dir($basedir.'/'.$file)) {
                        $fpdir = realpath($basedir.'/'.$file);
                        $allfiles['folders'][] = new Folder($fpdir);
                    } else {
                        $fpdir = realpath($basedir.'/'.$file);
                        $allfiles['files'][] = new File($fpdir);
                    }
                }
            }
            return $allfiles;
        } else {
        	if (Files::$backlog == false) {
	            if (0 === strpos(realpath($basedir.'/'.$dir),$basedir)) {
	                $files = array_diff(scandir($basedir.'/'.$dir), ['.','..']);
	                $allfiles = [];
	                $allfiles['folders'] = [];
	                $allfiles['files'] = [];
	                foreach ($files as $file) {
	                    if (in_array(pathinfo($file,PATHINFO_EXTENSION),Files::$alcontent) || is_dir($basedir.'/'.$dir.'/'.$file)) {
    	                    if (is_dir($basedir.'/'.$dir.'/'.$file)) {
    	                        $fpdir = realpath($basedir.'/'.$dir.'/'.$file);
    	                        $allfiles['folders'][] = new Folder($fpdir);
    	                    } else {
    	                        $fpdir = realpath($basedir.'/'.$dir.'/'.$file);
    	                        $allfiles['files'][] = new File($fpdir);
    	                    }
	                    }
	                }
	                return $allfiles;   
	            } else {
	                return false;
	            }
        	} else {
        		$files = array_diff(scandir($basedir.'/'.$dir), ['.','..']);
                $allfiles = [];
                $allfiles['folders'] = [];
                $allfiles['files'] = [];
                foreach ($files as $file) {
                    if (in_array(pathinfo($file,PATHINFO_EXTENSION),Files::$alcontent) || is_dir($basedir.'/'.$dir.'/'.$file)) {
                        if (is_dir($basedir.'/'.$dir.'/'.$file)) {
                            $fpdir = realpath($basedir.'/'.$dir.'/'.$file);
                            $allfiles['folders'][] = new Folder($fpdir);
                        } else {
                            $fpdir = realpath($basedir.'/'.$dir.'/'.$file);
                            $allfiles['files'][] = new File($fpdir);
                        }
                    }
                }
                return $allfiles;  
        	}
        }
        
        
    }
    /**
     * Upload a file
     */
    public function upload ($file, $dir = null) {
        $basedir = $this->fullpath;
        if (isset($file)) {
            $filename = preg_replace(Files::$rename," ",$file["name"]);
            if ($dir == null) {
                $canupload = 1;
                $fileType = pathinfo($filename, PATHINFO_EXTENSION);
                if ($file["size"] > Files::$maxsize) {
                    return false;
                }
                if (!in_array($fileType, Files::$alcontent)) {
                    return false;
                }
                if (file_exists($tfile)) {
                    return false;
                }
                if (move_uploaded_file($file["tmp_name"], "$basedir/{$filename}")) {
                    if (is_dir("$basedir/$dir/{$filename}")) {
                        return new Folder("$basedir/$dir/{$filename}");
                    } else {
                        return new File("$basedir/$dir/{$filename}");
                    }
                } else {
                    return false;
                }
            } else {
                if (Files::$backlog == false) {
                    if (0 === strpos(realpath($basedir.'/'.$dir),$basedir)) {
                        $canupload = 1;
                        $fileType = pathinfo($filename, PATHINFO_EXTENSION);
                        if ($file["size"] > 2 * 1024 * 1024) {
                            return false;
                        }
                        if (!in_array($fileType, Files::$alcontent)) {
                            return false;
                        }
                        if (file_exists($basedir.'/'.$dir.'/'.$filename)) {
                            return false;
                        }
                        if (move_uploaded_file($file["tmp_name"], "$basedir/$dir/{$filename}")) {
                            if (is_dir("$basedir/$dir/{$filename}")) {
                                return new Folder("$basedir/$dir/{$filename}");
                            } else {
                                return new File("$basedir/$dir/{$filename}");
                            }
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                } else {
                    $canupload = 1;
                    $fileType = pathinfo($filename, PATHINFO_EXTENSION);
                    if ($file["size"] > 2 * 1024 * 1024) {
                        return false;
                    }
                    if (!in_array($fileType, Files::$alcontent)) {
                        return false;
                    }
                    if (file_exists($basedir.'/'.$dir.'/'.$filename)) {
                        return false;
                    }
                    if (move_uploaded_file($file["tmp_name"], "$basedir/$dir/{$filename}")) {
                        if (is_dir("$basedir/$dir/{$filename}")) {
                            return new Folder("$basedir/$dir/{$filename}");
                        } else {
                            return new File("$basedir/$dir/{$filename}");
                        }
                    } else {
                        return false;
                    }
                }
            }
        } else {
            return false;
        }
    }
    /**
     * Create a new File
     */
    public function create ($filename, $type = "file", $dir = null, $content = null) {
        $basedir = $this->fullpath;
        $filename = preg_replace(Files::$rename," ",$filename);
        $fext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!in_array($fext, Files::$alcontent) && $type != "dir") {
            return false;
        }
        if (!file_exists($basedir)) {
            mkdir($basedir);
        }
        if (strlen($content) > Files::$maxsize) {
            return false;
        }
        if ($dir == null) {
            if (file_exists($basedir.'/'.$filename) && file_exists($basedir.'/'.$dir.'/')) {
                return false;
            } else {
                if ($type == "file") {
                    if ($content == null) {
                        file_put_contents($basedir.'/'.$filename, "");
                        return new File($basedir.'/'.$filename);
                    } else {
                        file_put_contents($basedir.'/'.$filename, $content);
                        return new File($basedir.'/'.$filename);
                    }
                } elseif ($type == "dir" || $type == "directory" || $type == "folder") {
                    mkdir($basedir.'/'.$filename);
                    return new Folder($basedir.'/'.$filename);
                }
            }
        } else {
            if (Files::$backlog == false) {
                if (0 === strpos(realpath($basedir.'/'.$dir),$basedir)) {
                    if (file_exists($basedir.'/'.$dir.'/'.$filename) && file_exists($basedir.'/'.$dir.'/')) {
                        return false;
                    } else {
                        if ($type == "file") {
                            if ($content == null) {
                                file_put_contents($basedir.'/'.$dir.'/'.$filename, "");
                                return new File($basedir.'/'.$dir.'/'.$filename);
                            } else {
                                file_put_contents($basedir.'/'.$dir.'/'.$filename, $content);
                                return new File($basedir.'/'.$dir.'/'.$filename);
                            }
                        } elseif ($type == "dir" || $type == "directory" || $type == "folder") {
                            mkdir($basedir.'/'.$dir.'/'.$filename);
                            return new Folder($basedir.'/'.$dir.'/'.$filename);
                        }
                    }
                }
            } else {
                if (file_exists($basedir.'/'.$dir.'/'.$filename) && file_exists($basedir.'/'.$dir.'/')) {
                    return false;
                } else {
                    if ($type == "file") {
                        if ($content == null) {
                            file_put_contents($basedir.'/'.$dir.'/'.$filename, "");
                            return new File($basedir.'/'.$dir.'/'.$filename);
                        } else {
                            file_put_contents($basedir.'/'.$dir.'/'.$filename, $content);
                            return new File($basedir.'/'.$dir.'/'.$filename);
                        }
                    } elseif ($type == "dir" || $type == "directory" || $type == "folder") {
                        mkdir($basedir.'/'.$dir.'/'.$filename);
                        return new Folder($basedir.'/'.$dir.'/'.$filename);
                    }
                }
            }
        }
        
    }
    /**
     * Delete folder
     */
    public function delete () {
        if (file_exists($this->fullpath)) {
            if (is_dir($this->fullpath) && count(array_diff(scandir($this->fullpath), ['.', '..'])) == 0) {
                if (rmdir($this->fullpath)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                function rrmdir($src) {
                    $dir = opendir($src);
                    while(false !== ( $file = readdir($dir)) ) {
                        if (( $file != '.' ) && ( $file != '..' )) {
                            $full = $src . '/' . $file;
                            if ( is_dir($full) ) {
                                rrmdir($full);
                            }
                            else {
                                unlink($full);
                            }
                        }
                    }
                    closedir($dir);
                    rmdir($src);
                }
                $rmdir = rrmdir($this->fullpath);
                if ($rmdir == null) {
                    return true;
                } elseif ($rmdir = true) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
    /**
     * Gets recursive directory size (in bytes)
     */
    function size ($dir = null) {
        if (is_null($dir)) {
            $dir = $this->fullpath;
        }
        $folder = array_diff(scandir($dir), ['.', '..']);
        $filesize = 0;
        foreach ($folder as $file) {
            if (is_dir($dir.'/'.$file)) {
                $filesize += $this->size($dir.'/'.$file);
            } else {
                $filesize += filesize($dir.'/'.$file);
            }
        }
        return $filesize;
    }
    public function zip () {
        $source = $this->fullpath;
        $pos = strpos($this->fullpath,$this->name);
        if ($pos !== false) {
            $destination = substr_replace($this->fullpath,'',$pos,strlen($this->name)).'/'.$this->name.'.zip';
        }
        if (extension_loaded('zip')) {
            if (file_exists($source)) {
                $zip = new ZipArchive();
                if ($zip->open($destination, ZIPARCHIVE::CREATE)) {
                    $source = realpath($source);
                    if (is_dir($source)) {
                        $iterator = new RecursiveDirectoryIterator($source);
                        // skip dot files while iterating 
                        $iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
                        $files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
                        foreach ($files as $file) {
                            $file = realpath($file);
                            if (is_dir($file)) {
                                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                            } else if (is_file($file)) {
                                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                            }
                        }
                    } else if (is_file($source)) {
                        $zip->addFromString(basename($source), file_get_contents($source));
                    }
                }
                $zip->close();
                return new File ($destination);
            }
        }
        return false;
    }
}