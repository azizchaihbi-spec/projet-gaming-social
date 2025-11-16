<?php
class Book {
    private string $title;
    private string $author;
    private string $publicationDate;
    private string $language;
    private string $status;
    private int $copies;
    private string $category;

    public function __construct($title, $author, $publicationDate, $language, $status, $copies, $category) {
        $this->title = $title;
        $this->author = $author;
        $this->publicationDate = $publicationDate;
        $this->language = $language;
        $this->status = $status;
        $this->copies = $copies;
        $this->category = $category;
    }

    public function show() {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Title</th><th>Author</th><th>Publication Date</th><th>Language</th><th>Status</th><th>Copies</th><th>Category</th></tr>";
        echo "<tr>";
        echo "<td>{$this->title}</td>";
        echo "<td>{$this->author}</td>";
        echo "<td>{$this->publicationDate}</td>";
        echo "<td>{$this->language}</td>";
        echo "<td>{$this->status}</td>";
        echo "<td>{$this->copies}</td>";
        echo "<td>{$this->category}</td>";
        echo "</tr>";
        echo "</table>";
    }
}
?>