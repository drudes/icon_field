<?php

declare(strict_types=1);

namespace Drupal\icon_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\icon_bundle_api\IconBundleManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of 'icon_field' formatter.
 *
 * @FieldFormatter(
 *  id = "icon_field_formatter",
 *  label = @Translation("Icon"),
 *  field_types = {
 *    "icon_field"
 *  }
 * )
 */
final class IconFieldFormatter extends FormatterBase {

  /**
   * @var \Drupal\icon_bundle_api\IconBundleManagerInterface
   */
  protected $iconBundleManager;

  /**
   *
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, IconBundleManagerInterface $icon_bundle_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->iconBundleManager = $icon_bundle_manager;
  }

  /**
   *
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $icon_bundle_manager = $container->get('plugin.manager.icon_bundle');
    return new self($plugin_id, $plugin_definition, $configuration['field_definiton'], $configuration['settings'], $configuration['label'], $configuration['view_mode'], $configuration['third_party_settings'], $icon_bundle_manager);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Displays the icon.');

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $bundle = $this->iconBundleManager->getDefinition($item->bundle);
      $elements[$delta] = [
        '#theme'   => 'icon_field',
        '#link'    => $item->icon_link,
        '#content' => ['#type' => $bundle['icon_element']] + self::propertize($item->icon_spec),
      ];
    }

    return $elements;
  }

  /**
   *
   */
  protected static function propertize(array $array): array {
    $result = [];
    foreach ($array as $key => $value) {
      $prop_key = (string) $key;
      if ('#' !== substr($prop_key, 0, 1)) {
        $prop_key = '#' . $prop_key;
      }
      $result[$prop_key] = $value;
    }

    return $result;
  }

}
