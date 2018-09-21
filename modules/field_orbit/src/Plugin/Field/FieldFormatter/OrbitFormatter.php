<?php

namespace Drupal\field_orbit\Plugin\Field\FieldFormatter;

use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'Orbit' formatter.
 *
 * @FieldFormatter(
 *   id = "orbit",
 *   label = @Translation("Zurb Orbit slider"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class OrbitFormatter extends ImageFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'animInFromRight' => 'slide-in-right',
      'animOutToRight' => 'slide-out-right',
      'animInFromLeft' => 'slide-in-left',
      'animOutToLeft' => 'slide-out-left',
      'caption' => '',
      'caption_link' => '',
      'autoPlay' => TRUE,
      'timerDelay' => 5000,
      'infiniteWrap' => TRUE,
      'swipe' => TRUE,
      'pauseOnHover' => TRUE,
      'accessible' => TRUE,
      'bullets' => TRUE,
      'navButtons' => TRUE,
      'containerClass' => 'orbit-container',
      'slideClass' => 'orbit-slide',
      'boxOfBullets' => 'orbit-bullets',
      'nextClass' => 'orbit-next',
      'prevClass' => 'orbit-previous',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    // Get image_style and image_link form elements from parent method.
    $element = parent::settingsForm($form, $form_state);

    $link_types = [
      'content' => t('Content'),
      'file' => t('File'),
    ];
    $captions = [
      'title'   => t('Title text'),
      'alt'     => t('Alt text'),
    ];

    $element['caption'] = [
      '#title'          => t('Caption'),
      '#type'           => 'select',
      '#default_value'  => $this->getSetting('caption'),
      '#empty_option'   => t('Nothing'),
      '#options'        => $captions,
    ];
    $element['caption_link'] = [
      '#title'          => t('Link caption to'),
      '#type'           => 'select',
      '#default_value'  => $this->getSetting('caption_link'),
      '#empty_option'   => t('Nothing'),
      '#options'        => $link_types,
      '#states' => [
        'invisible' => [
          ':input[name$="[settings_edit_form][settings][caption]"]' => ['value' => ''],
        ],
      ],
    ];
    $element['animInFromRight'] = [
      '#type' => 'select',
      '#title' => t('animInFromRight'),
      '#default_value' => $this->getSetting('animInFromRight'),
      '#options' => $this->getAnimationInOptions(),
    ];
    $element['animOutToRight'] = [
      '#type' => 'select',
      '#title' => t('animOutToRight'),
      '#default_value' => $this->getSetting('animOutToRight'),
      '#options' => $this->getAnimationOutOptions(),
    ];
    $element['animInFromLeft'] = [
      '#type' => 'select',
      '#title' => t('animInFromLeft'),
      '#default_value' => $this->getSetting('animInFromLeft'),
      '#options' => $this->getAnimationInOptions(),
    ];
    $element['animOutToLeft'] = [
      '#type' => 'select',
      '#title' => t('animOutToLeft'),
      '#default_value' => $this->getSetting('animOutToLeft'),
      '#options' => $this->getAnimationOutOptions(),
    ];
    $element['autoPlay'] = [
      '#type' => 'checkbox',
      '#title' => t('Autoplay'),
      '#default_value' => $this->getSetting('autoPlay'),
      '#description' => t('Allows Orbit to automatically animate on page load.'),
    ];
    $element['timerDelay'] = [
      '#type' => 'textfield',
      '#title' => t('Timer speed'),
      '#element_validate' => ['element_validate_integer_positive'],
      '#default_value' => $this->getSetting('timerDelay'),
      '#description' => t('Amount of time, in ms, between slide transitions'),
    ];
    $element['infiniteWrap'] = [
      '#type' => 'checkbox',
      '#title' => t('Infinite Wrap'),
      '#default_value' => $this->getSetting('infiniteWrap'),
      '#description' => t('Allows Orbit to infinitely loop through the slides.'),
    ];
    $element['swipe'] = [
      '#type' => 'checkbox',
      '#title' => t('Swipe'),
      '#default_value' => $this->getSetting('swipe'),
      '#description' => t('Allows the Orbit slides to bind to swipe events for mobile.'),
    ];
    $element['pauseOnHover'] = [
      '#type' => 'checkbox',
      '#title' => t('Pause on hover'),
      '#default_value' => $this->getSetting('pauseOnHover'),
      '#description' => t('Pause slideshow when you hover on the slide.'),
    ];
    $element['accessible'] = [
      '#type' => 'checkbox',
      '#title' => t('Keyboard events'),
      '#default_value' => $this->getSetting('accessible'),
      '#description' => t('Allows Orbit to bind keyboard events to the slider.'),
    ];
    $element['bullets'] = [
      '#type' => 'checkbox',
      '#title' => t('Bullets'),
      '#default_value' => $this->getSetting('bullets'),
      '#description' => t('Show bullets.'),
    ];
    $element['navButtons'] = [
      '#type' => 'checkbox',
      '#title' => t('Nav buttons'),
      '#default_value' => $this->getSetting('navButtons'),
      '#description' => t('Show navigations buttons.'),
    ];
    $element['containerClass'] = [
      '#type' => 'textfield',
      '#title' => t('Container Class'),
      '#default_value' => $this->getSetting('containerClass'),
      '#description' => t('Class applied to the container of Orbit'),
    ];
    $element['slideClass'] = [
      '#type' => 'textfield',
      '#title' => t('slide class'),
      '#default_value' => $this->getSetting('slideClass'),
      '#description' => t('Class applied to individual slides.'),
    ];
    $element['boxOfBullets'] = [
      '#type' => 'textfield',
      '#title' => t('Bullets class'),
      '#default_value' => $this->getSetting('boxOfBullets'),
      '#description' => t('Class applied to the bullet container.'),
    ];
    $element['nextClass'] = [
      '#type' => 'textfield',
      '#title' => t('Next class'),
      '#default_value' => $this->getSetting('nextClass'),
      '#description' => t('Class applied to the `next` navigation buton.'),
    ];
    $element['prevClass'] = [
      '#type' => 'textfield',
      '#title' => t('Prev class'),
      '#default_value' => $this->getSetting('prevClass'),
      '#description' => t('Class applied to the `previous` navigation button.'),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    // Get summary of image_style and image_link from parent method.
    $summary = parent::settingsSummary();

    $caption_types = [
      'title' => t('Title text'),
      'alt'   => t('Alt text'),
    ];

    $link_types = [
      'content' => t('Content'),
      'file' => t('File'),
    ];

    // Display this setting only if there's a caption.
    $caption_types_settings = $this->getSetting('caption');
    if (isset($caption_types[$caption_types_settings])) {
      $caption_message = t('Caption: @caption', ['@caption' => $caption_types[$caption_types_settings]]);
      $link_types_settings = $this->getSetting('caption_link');
      if (isset($link_types[$link_types_settings])) {
        $caption_message .= ' (' . t('Link to: @link', ['@link' => $link_types[$link_types_settings]]) . ')';
      }
      $summary[] = $caption_message;
    }
    $summary[] = t('animInFromLeft: @effect', ['@effect' => $this->getSetting('animInFromLeft')]);
    $summary[] = t('animInFromRight: @effect', ['@effect' => $this->getSetting('animInFromRight')]);
    $summary[] = t('animOutToLeft: @effect', ['@effect' => $this->getSetting('animOutToLeft')]);
    $summary[] = t('animOutToRight: @effect', ['@effect' => $this->getSetting('animOutToRight')]);
    $summary[] = t('Autoplay: @autoplay', ['@autoplay' =>$this->getSetting('infiniteWrap') ? t('yes') : t('no')]);
    $summary[] = t('Timer delay: @speedms', ['@speed' => $this->getSetting('timerDelay')]);
    $summary[] = t('Infinite wrap: @wrap', ['@wrap' => $this->getSetting('infiniteWrap') ? t('yes') : t('no')]);
    $summary[] = t('Swipe enabled: @swipe', ['@swipe' => $this->getSetting('swipe') ? t('yes') : t('no')]);
    $summary[] = t('Pause on hover: @pause', ['@pause' => $this->getSetting('pauseOnHover') ? t('yes') : t('no')]);
    $summary[] = t('Keyboard navigation: @accessible', ['@accessible' => $this->getSetting('accessible') ? t('yes') : t('no')]);
    $summary[] = t('bullets: @bullets', ['@bullets' => $this->getSetting('bullets') ? t('yes') : t('no')]);
    $summary[] = t('Navigation buttons: @nav', ['@nav' => $this->getSetting('navButtons') ? t('yes') : t('no')]);
    $summary[] = t('Container class: @class', ['@class' => $this->getSetting('containerClass')]);
    $summary[] = t('Slide class: @class', ['@class' => $this->getSetting('slideClass')]);
    $summary[] = t('Bullets class: @class', ['@class' => $this->getSetting('boxOfBullets')]);
    $summary[] = t('Next class: @class', ['@class' => $this->getSetting('nextClass')]);
    $summary[] = t('Previous class: @class', ['@class' => $this->getSetting('nextClass')]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    // Get image html from parent method.
    $images = parent::viewElements($items, $langcode);

    static $orbit_count;
    $orbit_count = (is_int($orbit_count)) ? $orbit_count + 1 : 1;

    $files = $this->getEntitiesToView($items, $langcode);

    $elements = [];
    $entity = [];
    $links = [
      'image_link' => 'path',
      'caption_link'  => 'caption_path',
    ];

    // Loop through required links (because image and
    // caption can have different links).
    foreach ($items as $delta => $item) {
      $uri = [];
      // Set Image caption.
      if ($this->getSetting('caption') != '') {
        $caption_settings = $this->getSetting('caption');
        if ($caption_settings == 'title') {
          $item_settings[$delta]['caption'] = $item->getValue()['title'];
        }
        elseif ($caption_settings == 'alt') {
          $item_settings[$delta]['caption'] = $item->getValue()['alt'];
        }
        $item->set('caption', $item_settings[$delta]['caption']);
      }
      // Set Image and Caption Link.
      foreach ($links as $setting => $path) {
        if ($this->getSetting($setting) != '') {
          switch ($this->getSetting($setting)) {
            case 'content':
              $entity = $item->getEntity();
              if (!$entity->isNew()) {
                $uri = $entity->urlInfo();
                $uri = !empty($uri) ? $uri : '';
                $item->set($path, $uri);
              }
              break;

            case 'file':
              foreach ($files as $file_delta => $file) {
                $image_uri = $file->getFileUri();
                $uri = Url::fromUri(file_create_url($image_uri));
                $uri = !empty($uri) ? $uri : '';
                $items[$file_delta]->set($path, $uri);
              }
              break;
          }
        }
      }
    }

    $defaults = $this->defaultSettings();

    if (count($items)) {
      // Only include non-default values to minimize html output.
      $options = [];
      foreach($defaults as $key => $setting) {
        // Don't pass these to orbit.
        if($key == 'caption_link' || $key == 'caption' || $key == 'image_style') {
          continue;
        }
        if($this->getSetting($key) != $setting) {
          $options[$key] = $this->getSetting($key);
        }
      }

      $elements[] = [
        '#theme' => 'field_orbit',
        '#items' => $items,
        '#options' =>$options,
        '#entity' => $entity,
        '#image' => $images,
        '#orbit_id' => $orbit_count,
      ];
    }

    return $elements;
  }

  /**
   * Array of animations out options.
   *
   * @return array
   */
  private function getAnimationOutOptions() {
    return [
      "Slide" => [
        "slide-out-down" => t("slide-out-down"),
        "slide-out-left" => t("slide-out-left"),
        "slide-out-up" => t("slide-out-up"),
        "slide-out-right" => t("slide-out-right"),
      ],
      "Fade"  => [
        "fade-out" => t("fade-out"),
      ],
      "Hinge" => [
        "hinge-out-from-top" => t("hinge-out-from-top"),
        "hinge-out-from-right" => t("hinge-out-from-right"),
        "hinge-out-from-bottom" => t("hinge-out-from-bottom"),
        "hinge-out-from-left" => t("hinge-out-from-left"),
        "hinge-out-from-middle-x" => t("hinge-out-from-middle-x"),
        "hinge-out-from-middle-y" => t("hinge-out-from-middle-y"),
      ],
      "Scale" => [
        "scale-out-up" => t("scale-out-up"),
        "scale-out-down" => t("scale-out-down"),
      ],
      "Spin"  => [
        "spin-out" => t("spin-out"),
        "spin-out-ccw" => t("spin-out-ccw"),
      ],
    ];
  }

  /**
   * Array of animation in options.
   *
   * @return array
   */
  private function getAnimationInOptions() {
    return [
      "Slide" => [
        "slide-in-down"=> t("slide-in-down"),
        "slide-in-left"=> t("slide-in-left"),
        "slide-in-up" => t("slide-in-up"),
        "slide-in-right" => t("slide-in-right"),
      ],
      "Fade"  => [
        "fade-in" => t("fade-in"),
      ],
      "Hinge" => [
        "hinge-in-from-top" => t("hinge-in-from-top"),
        "hinge-in-from-right" => t("hinge-in-from-right"),
        "hinge-in-from-bottom" => t("hinge-in-from-bottom"),
        "hinge-in-from-left" => t("hinge-in-from-left"),
        "hinge-in-from-middle-x" => t("hinge-in-from-middle-x"),
        "hinge-in-from-middle-y" => t("hinge-in-from-middle-y"),
      ],
      "Scale" => [
        "scale-in-up" => t("scale-in-up"),
        "scale-in-down" => t("scale-in-down"),
      ],
      "Spin"  => [
        "spin-in" => t("spin-in"),
        "spin-in-ccw" => t("spin-in-ccw"),
      ],
    ];
  }
}
