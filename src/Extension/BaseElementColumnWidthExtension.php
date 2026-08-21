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
    /**
     * Width dropdown stored on every BaseElement.
     *
     * The enum lists both the current large-N values and the legacy
     * fraction-based values ('1/4','1/3','1/2','2/3','full') so that content
     * saved before the large-N rewrite remains a valid, storable value -
     * WidthClass()/WidthRatio() below normalise legacy values on read.
     */
    private static array $db = [
        'Width' => "Enum('large-1,large-2,large-3,large-4,large-5,large-6,large-7,large-8,large-9,large-10,large-11,large-12,auto,shrink,1/4,1/3,1/2,2/3,full','large-12')",
        // 'Padding' => "Enum('none,20px,40px,60px','none')",
    ];

    private static array $defaults = [
        'Width' => 'large-12',
        // 'Padding' => '20px',
    ];

    /** Legacy fraction-based values (pre large-N rewrite) mapped to their large-N equivalent */
    private const LEGACY_WIDTH_CLASSES = [
        '1/4'  => 'large-3',
        '1/3'  => 'large-4',
        '1/2'  => 'large-6',
        '2/3'  => 'large-8',
        'full' => 'large-12',
    ];

    private const LEGACY_WIDTH_RATIOS = [
        '1/4'  => 0.25,
        '1/3'  => 0.3333,
        '1/2'  => 0.5,
        '2/3'  => 0.6667,
        'full' => 1.0,
    ];

   public function updateCMSFields(FieldList $fields): void
    {
        // Make sure we don’t accidentally show this on the grid itself or on top-level blocks
        /** @var \DNADesign\Elemental\Models\BaseElement $owner */
        $owner = $this->owner;

        $isNestedUnderGrid = ($owner->getPage() instanceof ElementGrid);
        $isElementGridItself = ($owner instanceof ElementGrid);

        // Always remove any previous instance first
        $fields->removeByName(['Width', 'Padding', 'VerticalAlign']);

        if (!$isNestedUnderGrid || $isElementGridItself) {
            // If not nested under ElementGrid(or is the ElementGrid), hide the field
            return;
        }

        // Show the width selector only for elements nested within ElementGrid
        $fields->findOrMakeTab('Root.Main', 'Main');

        $fields->addFieldToTab(
            'Root.Main',
            DropdownField::create('Width', 'Column Width')
                ->setSource([
                    'large-1'  => '1 col  (1/12 — ~8%)',
                    'large-2'  => '2 cols (1/6 — ~17%)',
                    'large-3'  => '3 cols (1/4 — 25%)',
                    'large-4'  => '4 cols (1/3 — 33%)',
                    'large-5'  => '5 cols (5/12 — ~42%)',
                    'large-6'  => '6 cols (1/2 — 50%)',
                    'large-7'  => '7 cols (7/12 — ~58%)',
                    'large-8'  => '8 cols (2/3 — 67%)',
                    'large-9'  => '9 cols (3/4 — 75%)',
                    'large-10' => '10 cols (5/6 — ~83%)',
                    'large-11' => '11 cols (11/12 — ~92%)',
                    'large-12' => '12 cols (Full — 100%)',
                    'auto'     => 'Auto (fill remaining space)',
                    'shrink'   => 'Shrink (fit content)',
                ])
                ->setEmptyString('- Choose Column Width -')
                ->setDescription('Controls how wide this block renders inside the grid (Foundation XY Grid, large breakpoint).'),
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
        $width = $this->owner->Width;

        return self::LEGACY_WIDTH_CLASSES[$width] ?? ($width ?: 'large-12');
    }

    /** Helper: numeric ratio if you need inline styles or calculations */
    public function WidthRatio(): float
    {
        $w = $this->owner->Width;

        if (isset(self::LEGACY_WIDTH_RATIOS[$w])) {
            return self::LEGACY_WIDTH_RATIOS[$w];
        }

        if (str_starts_with($w, 'large-')) {
            $cols = (int) substr($w, 6);
            return $cols > 0 ? round($cols / 12, 4) : 1.0;
        }

        return 0.0; // auto / shrink have no fixed ratio
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
