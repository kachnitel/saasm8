<?php

namespace App\Twig\Components;

use App\Entity\BillingCategory;
use App\Repository\BillingCategoryRepository;
use Symfony\Component\Validator\Constraints\CssColor;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;

#[AsLiveComponent]
class NewCategoryForm
{
    use ComponentToolsTrait;
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    #[LiveProp(writable: true)]
    #[NotBlank]
    public string $name = '';

    #[LiveProp(writable: true)]
    #[CssColor]
    public string $color = '#000';

    public function __construct()
    {
        $this->color = $this->getRandomColor();
    }

    #[LiveAction]
    public function saveCategory(BillingCategoryRepository $repository): void
    {
        $this->validate();

        $category = new BillingCategory();
        $category->setName($this->name);
        $category->setColor($this->color);

        $repository->save($category);

        $this->emit('category:created', [
            'category' => $category->getId()
        ]);

        $this->name = '';
        $this->color = $this->getRandomColor();
        $this->resetValidation();
    }

    private function getRandomColor(): string
    {
        return '#' . dechex(rand(0x000000, 0xFFFFFF));
    }
}
