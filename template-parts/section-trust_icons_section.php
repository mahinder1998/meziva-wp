<?php
/**
 * SECTION: Trust Icons / USPs (ACF FREE + Tailwind mz-)
 * Desktop: 4 items row
 * Mobile: slider + dots (no repeater, no library)
 */

if ( ! defined('ABSPATH') ) exit;

// Context: Options if present else Front page ID else current ID
$ctx = function_exists('acf_add_options_page') ? 'option' : (int) get_option('page_on_front');
if ( ! $ctx ) { $ctx = get_the_ID(); }

$enable = get_field('trust_icons_enable', $ctx);
if ($enable === null) $enable = true;
if ( ! $enable ) return;

$bg = (string) get_field('trust_icons_bg', $ctx);

// Helper: safe svg output
function mz_safe_svg($svg){
  if(!$svg) return '';
  return wp_kses($svg, [
    'svg' => [
      'xmlns'=>true,'viewBox'=>true,'width'=>true,'height'=>true,
      'fill'=>true,'stroke'=>true,'stroke-width'=>true,
      'stroke-linecap'=>true,'stroke-linejoin'=>true,'class'=>true
    ],
    'path'=>['d'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'stroke-linecap'=>true,'stroke-linejoin'=>true],
    'circle'=>['cx'=>true,'cy'=>true,'r'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true],
    'g'=>['fill'=>true,'stroke'=>true,'class'=>true,'transform'=>true],
    'line'=>['x1'=>true,'y1'=>true,'x2'=>true,'y2'=>true,'stroke'=>true,'stroke-width'=>true,'stroke-linecap'=>true],
    'polyline'=>['points'=>true,'fill'=>true,'stroke'=>true,'stroke-width'=>true,'stroke-linecap'=>true,'stroke-linejoin'=>true],
  ]);
}

// Build 4 items from ACF Free fields
$items = [];
for ($i=1; $i<=4; $i++){
  $title = (string) get_field("trust_{$i}_title", $ctx);
  $svg   = (string) get_field("trust_{$i}_icon_svg", $ctx);
  $img   = get_field("trust_{$i}_icon_image", $ctx);

  // Skip empty cards
  if(!$title && !$svg && empty($img)) continue;

  $img_url = '';
  $img_alt = $title;

  if(!empty($img)){
    $img_url = (is_array($img) && !empty($img['url']))
      ? $img['url']
      : (is_numeric($img) ? wp_get_attachment_image_url((int)$img, 'full') : '');
    if(is_array($img) && !empty($img['alt'])) $img_alt = $img['alt'];
  }

  $items[] = [
    'title' => $title,
    'svg'   => $svg,
    'img'   => $img_url,
    'alt'   => $img_alt,
  ];
}

if(empty($items)) return;

$section_id = 'mz-trust-icons-' . wp_generate_uuid4();
?>

<section
  id="<?php echo esc_attr($section_id); ?>"
  class="mz-w-full mz-py-10 md:mz-py-14"
  style="<?php echo $bg ? 'background-color:' . esc_attr($bg) . ';' : ''; ?>"
>
  <div class="mz-max-w-[1290px] mz-mx-auto mz-px-4 xl:mz-px-0">

    <!-- DESKTOP GRID -->
    <div class="mz-grid mz-grid-cols-2 xl:mz-grid-cols-4 mz-gap-10 lg:mz-gap-16 mz-items-start mz-justify-items-center">
      <?php foreach($items as $it): ?>
        <div class="mz-flex mz-flex-col mz-items-center mz-text-center mz-gap-4">
          <div class="">
            <?php if(!empty($it['img'])): ?>
              <img src="<?php echo esc_url($it['img']); ?>"
                   alt="<?php echo esc_attr($it['alt']); ?>"
                   class="mz-w-[88px] mz-h-[88px] mz-object-contain"
                   loading="lazy" decoding="async" />
            <?php else: ?>
              <div class="mz-w-[88px] mz-h-[88px] mz-text-[#c9a26b]"> 
                <?php echo mz_safe_svg($it['svg']); ?>
              </div>
            <?php endif; ?>
          </div>

          <?php if(!empty($it['title'])): ?>
            <div class="mz-font-bold mz-font-heading md:mz-text-nowrap mz-uppercase mz-tracking-wide mz-text-[16px] lg:mz-text-[20px] mz-text-[#6b3d2a]">
              <?php echo esc_html($it['title']); ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

   

  </div>
</section>
 