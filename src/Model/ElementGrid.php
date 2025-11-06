<?php

namespace Antlion\ElementalGrid\Model;

use DNADesign\Elemental\Models\BaseElement;
use DNADesign\Elemental\Models\ElementalArea;
use DNADesign\Elemental\Extensions\ElementalAreasExtension;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\DropdownField;

/**
 * @property int $ElementsID
 * @method ElementalArea Elements()
 */
class ElementGrid extends BaseElement
{
    private static string $icon = 'font-icon-thumbnails';

    private static array $db = [
        // Slashes are fine in Enum items. Default to full.
        'VerticalAlign' => "Enum('top,middle,bottom','middle')",
    ];

    private static array $defaults = [
        'VerticalAlign' => 'middle',
    ];

    private static array $has_one = [
        'Elements' => ElementalArea::class
    ];

    private static array $owns = [
        'Elements'
    ];

    private static array $cascade_deletes = [
        'Elements'
    ];

    private static array $cascade_duplicates = [
        'Elements'
    ];

    private static array $extensions = [
        ElementalAreasExtension::class
    ];

    private static string $table_name = 'ElementGrid';

    private static string $title = 'Group';

    private static string $description = 'Orderable grid of elements';

    private static string $singular_name = 'grid';

    private static string $plural_name = 'grids';

    public function getType(): string
    {
        return _t(__CLASS__ . '.BlockType', 'Grid');
    }

    /**
     * @return DBField
     */
    public function getSummary(): string
    {
        $count = $this->Elements()->Elements()->Count();
        $suffix = $count === 1 ? 'element': 'elements';

        return 'Contains ' . $count . ' ' . $suffix;
    }

    public function updateCMSFields(FieldList $fields): void
    {
        $fields->findOrMakeTab('Root.Settings', 'Settings');
        $fields->addFieldToTab(
            'Root.Settings',
            DropdownField::create('VerticalAlign', 'Vertical Alignment')
                ->setSource([
                    'top' => 'Top',
                    'middle' => 'Middle',
                    'bottom' => 'Bottom',
                ])->setEmptyString( '- Choose Vertical Alignment -')
        );
    }

    /**
     * Retrieve a elemental area relation name which this element owns
     *
     * @return string
     */
    public function getOwnedAreaRelationName(): string
    {
        $has_one = $this->config()->get('has_one');

        foreach ($has_one as $relationName => $relationClass) {
            if ($relationClass === ElementalArea::class && $relationName !== 'Parent') {
                return $relationName;
            }
        }

        return 'Elements';
    }

    public function inlineEditable(): bool
    {
        return false;
    }

    public function VerticalAlignClass(): string
    {
        return match ($this->owner->VerticalAlign) {
            'top' => 'align-top',     
            'middle' => 'align-middle',
            'bottom' => 'align-bottom',
            default => '',
        };
    }
}
