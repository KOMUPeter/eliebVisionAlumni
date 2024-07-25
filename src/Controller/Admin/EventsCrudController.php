<?php

namespace App\Controller\Admin;

use App\Entity\Events;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

// class EventsCrudController extends AbstractCrudController
// {
//     public static function getEntityFqcn(): string
//     {
//         return Events::class;
//     }

//     public function configureFields(string $pageName): iterable
//     {
//         return [
//             // IdField::new('id')->hideOnForm(),
//             TextField::new('title'),
//             TextField::new('description'),
//             DateTimeField::new('eventDate')->setFormat('yyyy-MM-dd HH:mm:ss'),
//             NumberField::new('cost'),
//         ];
//     }
// }


class EventsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Events::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            TextField::new('description'),
            DateTimeField::new('eventDate')->setFormat('yyyy-MM-dd HH:mm:ss'),
            NumberField::new('cost'),
            ChoiceField::new('type')
                ->setChoices([
                    'Disbursement' => 'Disbursement',
                    'Group Happenings' => 'GroupHappenings',
                ])
                ->setRequired(true),
            TextField::new('personInvolved'),          
        ];
    }
}

