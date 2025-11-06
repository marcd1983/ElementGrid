<?php

namespace Antlion\ElementalGrid\Extension;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use Antlion\ElementalGrid\Model\ElementGrid;
use SilverStripe\Forms\DropdownField;

/**
 * Adds width control to all Elemental blocks and preserves nested CMSEditLink behaviour.
 *
 * @property BaseElementColumnWidthExtension|$this $owner
 */
class BaseElementColumnWidthExtension extends Extension
{
    /** Width dropdown stored on every BaseElement */
    private static array $db = [
        // Slashes are fine in Enum items. Default to full.
        'Width' => "Enum('1/4,1/3,1/2,2/3,full','full')",
        // 'Padding' => "Enum('none,20px,40px,60px','none')",
    ];

    private static array $defaults = [
        'Width' => 'full',
        // 'Padding' => '20px',
    ];

   public function updateCMSFields(FieldList $fields): void
    {
        // Make sure we don’t accidentally show this on the grid itself or on top-level blocks
        /** @var \DNADesign\Elemental\Models\BaseElement $owner */
        $owner = $this->owner;

        $isNestedUnderGrid = ($owner->getPage() instanceof ElementGrid);
        $isElementGridItself = ($owner instanceof ElementGrid);

        // Always remove any previous instance first
        $fields->removeByName('Width', 'Padding', 'VerticalAlign');

        if (!$isNestedUnderGrid || $isElementGridItself) {
            // If not nested under ElementGrid(or is the ElementGrid), hide the field
            return;
        }

        // Show the width selector only for elements nested within ElementGrid
        $fields->findOrMakeTab('Root.Settings', 'Settings');

        $fields->addFieldToTab(
            'Root.Settings',
            DropdownField::create('Width', 'Column Width')
                ->setSource([
                    '1/4' => '¼ (1/4)',
                    '1/3' => '⅓ (1/3)',
                    '1/2' => '½ (1/2)',
                    '2/3' => '⅔ (2/3)',
                    'full' => 'Full width',
                ])
                ->setEmptyString( '- Choose Column Width -')
                ->setDescription('Controls how wide this block should render inside the grid.'),
        );

        // $fields->addFieldToTab(
        //     'Root.Settings',
        //     DropdownField::create('Padding', 'Padding Size')
        //         ->setSource([
        //             'none' => 'None',
        //             '20px' => '20px',
        //             '40px' => '40px',
        //             '60px' => '60px',
        //         ])
        //         ->setEmptyString( '- Choose Padding Size -')
        //         ->setDescription('Padding Size.'),

        // );

    }

    /** Helper: CSS class you can use on the block wrapper */
    public function WidthClass(): string
    {
        return match ($this->owner->Width) {
            '1/4' => 'large-3',
            '1/3' => 'large-4',
            '1/2' => 'large-6',
            '2/3' => 'large-8',
            'full' => 'large-12',
            default => '',
        };
    }
    /** Helper: numeric ratio if you need inline styles or calculations */
    public function WidthRatio(): float
    {
        return match ($this->owner->Width) {
            '1/4' => 0.25,
            '1/3' => 0.3333,
            '1/2' => 0.5,
            '2/3' => 0.6667,
            default => 1.0,
        };
    }
    // public function PaddingClass(): string
    // {
    //     return match ($this->owner->Padding) {
    //         'none' => '',
    //         '20px' => 'p-20',
    //         '40px' => 'p-40',
    //         '60px' => 'p-60',
    //         default => 'p-20',
    //     };
    // }
}
