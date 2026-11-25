<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Назва книги обовʼязкова')]
    #[Assert\Length(min: 1, max: 255)]
    private ?string $title = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Вкажіть рік видання')]
    #[Assert\Range(min: 1450, max: 2026, notInRangeMessage: 'Рік видання має бути від {{ min }} до {{ max }}')]
    private ?int $publishedYear = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'Кількість сторінок має бути більшою за 0')]
    private ?int $pages = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Оберіть автора')]
    private ?Author $author = null;

    public function __toString(): string
    {
        return $this->title ?? 'Нова книга';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getPublishedYear(): ?int
    {
        return $this->publishedYear;
    }

    public function setPublishedYear(int $publishedYear): static
    {
        $this->publishedYear = $publishedYear;
        return $this;
    }

    public function getPages(): ?int
    {
        return $this->pages;
    }

    public function setPages(int $pages): static
    {
        $this->pages = $pages;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): static
    {
        $this->author = $author;
        return $this;
    }
}