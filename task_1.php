<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
class TelegraphText 
{
    public $title; 
    public $text;
    public $author; 
    public $published; 
    public $slug; 

    public function __construct($author, $slug)
    {
        $this->author = $author;
        $this->slug = $slug;
        $this->published = date('Y-m-d H:i:s');
    }

    public function storeText()
    {
        $storeText = [
            'text' => $this->text, 
            'title' => $this->title, 
            'author' => $this->author, 
            'published' => $this->published
        ];
        $serialize = serialize($storeText);
        // данные, которые храняться в serialize нужно поместить в файл чье имя храниться в slug
        file_put_contents($this->slug, $serialize);
    }

    public function loadText()
    {
        $fileStorage = new FileStorage;
        $obj = $fileStorage->read(null, $this->slug);
        if (is_object($obj)) {
            $this->author = $obj->author;
            $this->text = $obj->text;
            $this->title = $obj->title;
            $this->published = $obj->published;
            return $this->text;
        } else {
            return false;
        }
    }
    
    public function editText($text, $title)
    {
        $this->text = $text;
        $this->title = $title;
    }
}


abstract class Storage 
{
    abstract function create($obj); 
    abstract function read ($id, $slug); 
    abstract function update ($id, $slug, $obj); 
    abstract function delete ($id, $slug); 
    abstract function list(); 
}

abstract class View 
{
    public $storage;

    abstract function displayTextById ($id); 
    abstract function displayTextByUrl ($url); 
    
    public function __construct($storage) 
    {
        $this->storage = $storage;
    }
    

} 

abstract class User 
{
    public $id; 
    public $name; 
    public $role; 

    abstract function getTextsToEdit(); 
} 

class FileStorage extends Storage
{
    function create($obj) 
    {
        $now = new DateTime();
        $date = $now->format('Y-m-d');
        $slug = $obj->slug;
        $slug = explode('.', $slug);
        print_r($slug);
        echo $fileName = $slug[0] . '_' . $date . '.' . $slug[1];
        $i = 0;
        while (file_exists($fileName)) {
            $i++;
            echo $fileName = $slug[0] . '_' . $date .  '_' . $i . '.'  . $slug[1];
        }
        $obj->slug = $fileName;
        $serialize = serialize ($obj);
        file_put_contents ($fileName, $serialize);
        return $fileName; 
    }
    function read($id, $slug) 
    {   
        if (file_exists($slug)) { 
            $serialize = file_get_contents($slug); 
            $obj = unserialize ($serialize); 
            return $obj;
        } 
        
    }
    function update($id, $slug, $obj)
    {
        if (file_exists($slug)) {
            $serialize = serialize ($obj);
            file_put_contents ($slug, $serialize);
        }
    }
    function delete($id, $slug)
    {
        if (file_exists($slug)) {
            unlink($slug);
        }
    }
    function list()
    {
        $files = scandir(__DIR__);
        print_r ($files);
        foreach ($files as $value) {
            
            $pos = strpos($value, '.txt');
            if ($pos != false) {
                echo $value;
                $files_true[] = $value;
            }
        }
    print_r ($files_true); 
    }
}
$TelegraphText = new TelegraphText('viktor', 'textclass.txt');
$TelegraphText->editText('hi', 'text');
$TelegraphText->storeText();
var_dump($TelegraphText->loadText());
echo '<pre>';
var_dump($TelegraphText);
$serialize = serialize ($TelegraphText);
var_dump($serialize);
$fileStorage = new FileStorage;
$fileName = $fileStorage->create($TelegraphText);
$fileStorage->update(null, $fileName, $TelegraphText);
var_dump($TelegraphText);
echo $fileName;
$fileStorage->list();

