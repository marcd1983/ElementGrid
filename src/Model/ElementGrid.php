<?php

namespace Antlion\ElementalGrid\Model;

use DNADesign\Elemental\Models\BaseElement;
use DNADesign\Elemental\Models\ElementalArea;
use DNADesign\Elemental\Extensions\ElementalAreasExtension;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\CheckboxField;

/**
 * @property string $VerticalAlign
 * @property int $ElementsID
 * @method ElementalArea Elements()
 */
class ElementGrid extends BaseElement
{
    private static string $icon = 'font-icon-block-layout-5';

    private static array $db = [
        'VerticalAlign' => "Enum('top,middle,bottom','middle')",
        'HorizontalAlign' => "Enum('left,center,right,justify','center')",
        'NoGridSpace' => 'Boolean',
    ];

    private static array $defaults = [
        'VerticalAlign' => 'middle',
        'HorizontalAlign' => 'center',
    ];

    private static array $has_one = [
        'Elements' => ElementalArea::class,
    ];

    private static array $owns = [
        'Elements',
    ];

    private static array $cascade_deletes = [
        'Elements',
    ];

    private static array $cascade_duplicates = [
        'Elements',
    ];

    private static array $extensions = [
        ElementalAreasExtension::class,
    ];

    private static string $table_name = 'ElementGrid';

    private static string $singular_name = 'grid';

    private static string $plural_name = 'grids';

    private static string $description = 'Orderable grid of elements';

    public function getType(): string
    {
        return _t(__CLASS__ . '.BlockType', 'Variable Column Grid');
    }

    public function getSummary(): string
    {
        $count = $this->Elements()->Elements()->Count();
        $suffix = $count === 1 ? 'element' : 'elements';

        return 'Contains ' . $count . ' ' . $suffix;
    }

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        // BaseElementColumnWidthExtension strips VerticalAlign from every
        // BaseElement (including this one); HorizontalAlign and NoGridSpace
        // are also removed here so re-adding them below never produces a
        // duplicate-named field alongside the default auto-scaffolded one.
        $fields->removeByName(['VerticalAlign', 'HorizontalAlign', 'NoGridSpace']);

        $fields->addFieldToTab(
            'Root.Main',
            DropdownField::create('VerticalAlign', 'Vertical Alignment')
                ->setSource([
                    'top'    => 'Top',
                    'middle' => 'Middle',
                    'bottom' => 'Bottom',
                ])
                ->setEmptyString('- Choose Vertical Alignment -')
        );

        $fields->addFieldToTab(
            'Root.Main',
            DropdownField::create('HorizontalAlign', 'Horizontal Alignment')
                ->setSource([
                    'left'    => 'Left',
                    'center'  => 'Center',
                    'right'   => 'Right',
                    'justify' => 'Justify',
                ])
                ->setEmptyString('- Choose Horizontal Alignment -')
        );

        $fields->addFieldToTab(
            'Root.Main',
            CheckboxField::create('NoGridSpace', 'Remove grid spacing (no gap between cells)')
        );

        return $fields;
    }

    /**
     * Used by ElementalAreasExtension to know which owned relation is the nested area.
     */
    public function getOwnedAreaRelationName(): string
    {
        return 'Elements';
    }

    public function inlineEditable(): bool
    {
        return false;
    }

    public function VerticalAlignClass(): string
    {
        return match ($this->VerticalAlign) {
            'top' => 'align-top',
            'middle' => 'align-middle',
            'bottom' => 'align-bottom',
            default => '',
        };
    }

    public function HorizontalAlignClass(): string
    {
        return match ($this->HorizontalAlign) {
            'left' => 'align-left',
            'center' => 'align-center',
            'right' => 'align-right',
            'justify' => 'align-justify',
            default => '',
        };
    }
}