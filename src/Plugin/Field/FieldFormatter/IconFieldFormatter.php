<?php declare(strict_types=1);

namespace Drupal\icon_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\icon_bundle_api\IconBundleManager;

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
class IconFieldFormatter extends FormatterBase
{
    /**
     * {@inheritdoc}
     */
    public function settingsForm(array $form, FormStateInterface $form_state)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function settingsSummary()
    {
        $summary = [];
        $summary[] = $this->t('Displays the icon.');

        return $summary;
    }

    /**
     * {@inheritdoc}
     */
    public function viewElements(FieldItemListInterface $items, $langcode)
    {
        $elements = [];

        foreach ($items as $delta => $item) {
            $bundle = IconBundleManager::getIconBundle($item->bundle);
            $elements[$delta] = [
                '#theme'   => 'icon_field',
                '#link'    => $item->icon_link,
                '#content' => ['#type' => $bundle['icon_element']] + self::propertize($item->icon_spec),
            ];
        }

        return $elements;
    }

    protected static function propertize(array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $prop_key = (string) $key;
            if ('#' !== substr($prop_key, 0, 1)) {
                $prop_key = '#'.$prop_key;
            }
            $result[$prop_key] = $value;
        }

        return $result;
    }
}
