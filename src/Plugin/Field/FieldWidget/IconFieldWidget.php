<?php

declare(strict_types=1);

namespace Drupal\icon_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of 'icon_field' widget.
 *
 * @FieldWidget(
 *  id = "icon_field_widget",
 *  label = @Translation("Icon"),
 *  module = "icon_field",
 *  field_types = {
 *    "icon_field"
 *  }
 * )
 */
class IconFieldWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $field_name = $items->getFieldDefinition()->getName();
    $element_parents = array_merge($element['#field_parents'], [$field_name, $delta]);
    $element_use_link_name = self::nestedElementName($element_parents, 'use_link');

    $item = $items[$delta];
    $element['#type'] = 'icon_picker';
    $element['#bundle'] = [
      '#default_value' => $item->get('bundle')->getValue() ?? '',
      '#weight'        => 100,
    ];
    $element['#icon_spec'] = [
      '#default_value' => $item->get('icon_spec')->getValue() ?? [],
      '#weight'        => 200,
    ];

    //
    // Additional inputs defined by IconFieldWidget
    // .
    $element['use_link'] = [
      '#type'          => 'checkbox',
      '#default_value' => $item->get('use_link')->getValue(),
    ] + ($element['#use_link'] ?? []) + [
      '#title'       => t('Wrap Link around the Icon'),
      '#description' => t('When checked wraps an anchor tag with the link from the next field.'),
      '#weight'      => 300,
    ];

    $element['icon_link'] = [
      '#type'          => 'textfield',
      '#default_value' => $item->get('icon_link')->getValue(),
      '#states'        => [
        'invisible' => [
                  [':input[name="' . $element_use_link_name . '"]' => ['checked' => FALSE]],
        ],
      ],
    ] + ($element['#icon_link'] ?? []) + [
      '#title'  => t('Icon Link'),
      '#weight' => 400,
    ];

    return $element;
  }

  /**
   * Provided ``$parents=['foo', bar']`` and ``$name='gez'`` it returns ``'foo[bar][gez]'``.
   */
  protected static function nestedElementName(array $parents, string $name): string {
    if (0 === count($parents)) {
      return $name;
    }

    $root = array_shift($parents);
    $keys = array_merge($parents, [$name]);

    return $root . '[' . implode('][', $keys) . ']';
  }

}
