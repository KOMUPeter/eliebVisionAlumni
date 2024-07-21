<?php

namespace App\Controller\Admin;

use App\Entity\Payout;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class PayoutCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Payout::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            NumberField::new('amount'),
            DateTimeField::new('paydate')->setFormat('yyyy-MM-dd HH:mm:ss'),
            AssociationField::new('user'),
            DateField::new('month')->setFormat('yyyy-MM-dd'),
        ];
    }
}

