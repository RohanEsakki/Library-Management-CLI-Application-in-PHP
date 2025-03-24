<?php
abstract class Library {
    protected $id;
    
    public function __construct($id) {
        if (!is_numeric($id) || $id <= 0) {
            throw new InvalidArgumentException("Invalid ID. It must be a positive number.");
        }
        $this->id = (int) $id;
    }
    
    abstract public function display();
}

class Author {
    public $name;
    
    public function __construct($name) {
        if (empty($name) || !preg_match("/^[a-zA-Z\s]+$/", $name)) {
            throw new InvalidArgumentException("Invalid Author name. Only letters and spaces allowed.");
        }
        $this->name = trim($name);
    }
}

class Book extends Library {
    private $title;
    private $publisher;
    private $author;
    private $isbn;
    private $genre;
    private static $file = 'books.json';

    public function __construct($id, $title, $publisher, $author, $isbn, $genre) {
        parent::__construct($id);
        $this->validateString($title, "Title");
        $this->validateString($publisher, "Publisher");
        $this->validateString($isbn, "ISBN");
        $this->validateString($genre, "Genre");

        $this->title = trim($title);
        $this->publisher = trim($publisher);
        $this->author = new Author($author);
        $this->isbn = trim($isbn);
        $this->genre = trim($genre);
    }

    private function validateString($value, $field) {
        if (empty($value) || !preg_match("/^[a-zA-Z0-9\s\-]+$/", $value)) {
            throw new InvalidArgumentException("Invalid $field. Only letters, numbers, spaces, and hyphens allowed.");
        }
    }

    public function add() {
        $books = file_exists(self::$file) ? json_decode(file_get_contents(self::$file), true) : [];
        $books[] = [
            'id' => $this->id,
            'title' => $this->title,
            'publisher' => $this->publisher,
            'author' => $this->author->name,
            'isbn' => $this->isbn,
            'genre' => $this->genre
        ];
        file_put_contents(self::$file, json_encode($books, JSON_PRETTY_PRINT));
        return "Book added successfully!\n";
    }

    public static function delete($id) {
        $books = file_exists(self::$file) ? json_decode(file_get_contents(self::$file), true) : [];
        $books = array_values(array_filter($books, fn($book) => $book['id'] != $id));
        file_put_contents(self::$file, json_encode($books, JSON_PRETTY_PRINT));
        return "Book deleted successfully!\n";
    }

    public static function listAll() {
        return file_exists(self::$file) ? json_decode(file_get_contents(self::$file), true) : [];
    }

    public static function listAscending() {
        $books = self::listAll();
        usort($books, fn($a, $b) => strcmp($a['title'], $b['title']));
        
        if (empty($books)) {
            echo "No books available.\n";
            return;
        }
    
        foreach ($books as $book) {
            echo "ID: {$book['id']}, Title: {$book['title']}, Publisher: {$book['publisher']}, ";
            echo "Author: {$book['author']}, ISBN: {$book['isbn']}, Genre: {$book['genre']}\n";
        }
    }
    
    public static function listDescending() {
        $books = self::listAll();
        usort($books, fn($a, $b) => strcmp($b['title'], $a['title']));
        
        if (empty($books)) {
            echo "No books available.\n";
            return;
        }
    
        foreach ($books as $book) {
            echo "ID: {$book['id']}, Title: {$book['title']}, Publisher: {$book['publisher']}, ";
            echo "Author: {$book['author']}, ISBN: {$book['isbn']}, Genre: {$book['genre']}\n";
        }
    }
    
    public function display() {
        echo "Book ID: {$this->id}, Title: {$this->title}, Publisher: {$this->publisher}, ";
        echo "Author: {$this->author->name}, ISBN: {$this->isbn}, Genre: {$this->genre}\n";
    }
}

class Resource extends Library{
    private $name;
    private $description;
    private $category;
    private $type;
    private static $file = 'resource.json';
    public function __construct($id, $name, $description, $category, $type){
        parent::__construct($id);
        $this->name = trim($name);
        $this->description = trim($description);
        $this->category = trim($category);
        $this->type = trim($type);
    }
       
    public function add() {
        $resources = file_exists(self::$file) ? json_decode(file_get_contents(self::$file), true) : [];
        $resources[] = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'type' => $this->type
        ];
        file_put_contents(self::$file, json_encode($resources, JSON_PRETTY_PRINT));
        return "Resource added Successfully!";
    }
    public static function delete($id){
        $resources = file_exists(self::$file) ? json_decode(file_get_contents(self::$file), true) : [];
        $resources = array_values(array_filter($resources, fn($resource) => $resource['id'] != $id));
        file_put_contents(self::$file, json_encode($resources, JSON_PRETTY_PRINT));
        return "Resource deleted Successfully!";
    }
    public static function validateId($id) {
        if (!is_numeric($id) || $id <= 0) {
            throw new InvalidArgumentException("Invalid ID. It must be a positive number.");
        }
    }    
    public static function listall(){
        return file_exists(self::$file) ? json_decode(file_get_contents(self::$file), true) : [];
    }
    
    public function display(){
        echo "Resource: {$this->name}, Description: {$this->description}, Category: {$this->category}, Type: {$this->type}\n";
    }
}
function menu() {
    while (true) {
        echo "\nLibrary Management System\n";
        echo "1. Add Book\n";
        echo "2. Delete Book\n";
        echo "3. List Books\n";
        echo "4. Search Book by ID\n";
        echo "5. Sort Books Ascending\n";
        echo "6. Sort Books Descending\n";
        echo "7. Add Other resource\n";
        echo "8. Delete Other resource\n";
        echo "9. List Other resource\n";
        echo "10. Exit\n";
        
        $choice = (int) readline("Choose an option: ");

        try {
            switch ($choice) {
                case 1:
                    $id = (int) readline("Enter Book ID: ");
                    $title = readline("Enter Title: ");
                    $publisher = readline("Enter Publisher: ");
                    $author = readline("Enter Author: ");
                    $isbn = readline("Enter ISBN: ");
                    $genre = readline("Enter Genre: ");
                    $book = new Book($id, $title, $publisher, $author, $isbn, $genre);
                    echo $book->add();
                    break;

                case 2:
                    $id = (int) readline("Enter Book ID to delete: ");
                    echo Book::delete($id);
                    break;

                case 3:
                    $books = Book::listAll();
                    if (empty($books)) {
                        echo "No books available.\n";
                    } else {
                        foreach ($books as $book) {
                            echo "ID: {$book['id']}, Title: {$book['title']}, Publisher: {$book['publisher']}, ";
                            echo "Author: {$book['author']}, ISBN: {$book['isbn']}, Genre: {$book['genre']}\n";
                        }
                    }
                    break;

                case 4:
                    $id = (int) readline("Enter Book ID to search: ");
                    $books = Book::listAll();
                    $found = false;
                    foreach ($books as $book) {
                        if ($book['id'] == $id) {
                            echo "ID: {$book['id']}, Title: {$book['title']}, Publisher: {$book['publisher']}, ";
                            echo "Author: {$book['author']}, ISBN: {$book['isbn']}, Genre: {$book['genre']}\n";
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        echo "Book not found.\n";
                    }
                    break;

                case 5:
                    echo "Books sorted in ascending order:\n";
                    Book::listAscending();
                    break;

                case 6:
                    echo "Books sorted in descending order:\n";
                    Book::listDescending();
                    break;

                case 7:
                    $id = (int) readline("Enter Resource ID: ");
                    $name = readline("Enter Resource Name: ");
                    $description = readline("Enter Resource Description: ");
                    $category = readline("Enter Resource Category: ");
                    $type = readline("Enter Resource Type: ");
                    $resource = new Resource($id, $name, $description, $category, $type);
                    echo $resource->add();                   
                    break;
    
                case 8:
                    $id = (int) readline("Enter Resource ID to delete: ");
                    echo Resource::delete($id);
                    break;
    
                case 9:
                    $resources = Resource::listAll();
                    if (empty($resources)) {
                        echo "No resources available.\n";
                    } else {
                        foreach ($resources as $resource) {
                            echo "ID: {$resource['id']}, Name: {$resource['name']}, Description: {$resource['description']}, Category: {$resource['category']}, Type: {$resource['type']}\n";
                        }
                    }
                    break;
    
                case 10:
                    exit("Exiting...\n");
    
                default:
                    echo "Invalid option, please try again!\n";
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}

menu();
?>