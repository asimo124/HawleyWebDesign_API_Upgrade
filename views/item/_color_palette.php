<?php

use app\models\Item;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $inputId string id of the color text input (preview badge follows it) */

// Materialize CSS color palette (same values as Material Design 2).
// Shade order: lighten-2, lighten-1, base, darken-1..4, accent-1..4.
$shadeNames = ['lighten-2', 'lighten-1', 'base', 'darken-1', 'darken-2', 'darken-3', 'darken-4', 'accent-1', 'accent-2', 'accent-3', 'accent-4'];
$palette = [
    'red'         => ['#e57373', '#ef5350', '#f44336', '#e53935', '#d32f2f', '#c62828', '#b71c1c', '#ff8a80', '#ff5252', '#ff1744', '#d50000'],
    'pink'        => ['#f06292', '#ec407a', '#e91e63', '#d81b60', '#c2185b', '#ad1457', '#880e4f', '#ff80ab', '#ff4081', '#f50057', '#c51162'],
    'purple'      => ['#ba68c8', '#ab47bc', '#9c27b0', '#8e24aa', '#7b1fa2', '#6a1b9a', '#4a148c', '#ea80fc', '#e040fb', '#d500f9', '#aa00ff'],
    'deep-purple' => ['#9575cd', '#7e57c2', '#673ab7', '#5e35b1', '#512da8', '#4527a0', '#311b92', '#b388ff', '#7c4dff', '#651fff', '#6200ea'],
    'indigo'      => ['#7986cb', '#5c6bc0', '#3f51b5', '#3949ab', '#303f9f', '#283593', '#1a237e', '#8c9eff', '#536dfe', '#3d5afe', '#304ffe'],
    'blue'        => ['#64b5f6', '#42a5f5', '#2196f3', '#1e88e5', '#1976d2', '#1565c0', '#0d47a1', '#82b1ff', '#448aff', '#2979ff', '#2962ff'],
    'light-blue'  => ['#4fc3f7', '#29b6f6', '#03a9f4', '#039be5', '#0288d1', '#0277bd', '#01579b', '#80d8ff', '#40c4ff', '#00b0ff', '#0091ea'],
    'cyan'        => ['#4dd0e1', '#26c6da', '#00bcd4', '#00acc1', '#0097a7', '#00838f', '#006064', '#84ffff', '#18ffff', '#00e5ff', '#00b8d4'],
    'teal'        => ['#4db6ac', '#26a69a', '#009688', '#00897b', '#00796b', '#00695c', '#004d40', '#a7ffeb', '#64ffda', '#1de9b6', '#00bfa5'],
    'green'       => ['#81c784', '#66bb6a', '#4caf50', '#43a047', '#388e3c', '#2e7d32', '#1b5e20', '#b9f6ca', '#69f0ae', '#00e676', '#00c853'],
    'light-green' => ['#aed581', '#9ccc65', '#8bc34a', '#7cb342', '#689f38', '#558b2f', '#33691e', '#ccff90', '#b2ff59', '#76ff03', '#64dd17'],
    'lime'        => ['#dce775', '#d4e157', '#cddc39', '#c0ca33', '#afb42b', '#9e9d24', '#827717', '#f4ff81', '#eeff41', '#c6ff00', '#aeea00'],
    'yellow'      => ['#fff176', '#ffee58', '#ffeb3b', '#fdd835', '#fbc02d', '#f9a825', '#f57f17', '#ffff8d', '#ffff00', '#ffea00', '#ffd600'],
    'amber'       => ['#ffd54f', '#ffca28', '#ffc107', '#ffb300', '#ffa000', '#ff8f00', '#ff6f00', '#ffe57f', '#ffd740', '#ffc400', '#ffab00'],
    'orange'      => ['#ffb74d', '#ffa726', '#ff9800', '#fb8c00', '#f57c00', '#ef6c00', '#e65100', '#ffd180', '#ffab40', '#ff9100', '#ff6d00'],
    'deep-orange' => ['#ff8a65', '#ff7043', '#ff5722', '#f4511e', '#e64a19', '#d84315', '#bf360c', '#ff9e80', '#ff6e40', '#ff3d00', '#dd2c00'],
    'brown'       => ['#a1887f', '#8d6e63', '#795548', '#6d4c41', '#5d4037', '#4e342e', '#3e2723'],
    'grey'        => ['#e0e0e0', '#bdbdbd', '#9e9e9e', '#757575', '#616161', '#424242', '#212121'],
    'blue-grey'   => ['#90a4ae', '#78909c', '#607d8b', '#546e7a', '#455a64', '#37474f', '#263238'],
];

// Reverse lookup so "colors you already use" can show their Materialize name.
$nameByHex = [];
foreach ($palette as $family => $hexes) {
    foreach ($hexes as $i => $hex) {
        $nameByHex[$hex] = $family . ' ' . $shadeNames[$i];
    }
}

$used = Item::find()
    ->select(['color', 'COUNT(*) AS n', "GROUP_CONCAT(title ORDER BY title SEPARATOR ', ') AS titles"])
    ->groupBy('color')
    ->orderBy(['n' => SORT_DESC, 'color' => SORT_ASC])
    ->asArray()
    ->all();

/** Black or white text, whichever reads better on $hex. */
$textOn = static function ($hex) {
    $hex = ltrim((string) $hex, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    if (!ctype_xdigit($hex) || strlen($hex) !== 6) {
        return '#000';
    }
    [$r, $g, $b] = array_map('hexdec', str_split($hex, 2));
    return (0.299 * $r + 0.587 * $g + 0.114 * $b) > 160 ? '#000' : '#fff';
};

$chip = static function ($hex, $title, $label = null) use ($textOn) {
    return Html::button(Html::encode($label ?? $hex), [
        'class' => 'color-chip',
        'style' => "background-color: {$hex}; color: {$textOn($hex)};",
        'data-copy' => $hex,
        'title' => $title,
    ]);
};
?>

<div class="color-palette card mb-4" data-color-input="<?= Html::encode($inputId) ?>">
    <div class="card-header d-flex flex-wrap align-items-center gap-2">
        <strong class="me-auto">Colors</strong>
        <small class="text-muted">Click a color to copy its hex code, then paste it into Color.</small>
        <span class="color-copied badge bg-success" role="status" aria-live="polite"></span>
    </div>
    <div class="card-body">
        <?php if ($used): ?>
            <h6 class="mb-2">Already used by your items</h6>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <?php foreach ($used as $row):
                    $hex = strtolower(trim((string) $row['color']));
                    $name = $nameByHex[$hex] ?? 'custom';
                    $title = "{$hex} ({$name}), {$row['n']} item" . ($row['n'] == 1 ? '' : 's') . ": {$row['titles']}";
                    echo $chip($hex, $title, "{$hex} · {$row['n']}");
                endforeach; ?>
            </div>
        <?php endif; ?>

        <h6 class="mb-2">Materialize palette</h6>
        <div class="table-responsive">
            <table class="table table-sm table-borderless align-middle mb-0 palette-table">
                <thead>
                    <tr>
                        <th></th>
                        <?php foreach ($shadeNames as $shade): ?>
                            <th class="small text-muted fw-normal text-center"><?= Html::encode($shade) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($palette as $family => $hexes): ?>
                        <tr>
                            <th class="small text-nowrap fw-semibold"><?= Html::encode($family) ?></th>
                            <?php foreach ($shadeNames as $i => $shade): ?>
                                <td class="p-1">
                                    <?= isset($hexes[$i]) ? $chip($hexes[$i], "{$family} {$shade}") : '' ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$this->registerCss(<<<CSS
.color-palette .color-chip {
    border: 1px solid rgba(0, 0, 0, .15);
    border-radius: .375rem;
    font: 600 .75rem/1 ui-monospace, SFMono-Regular, Menlo, monospace;
    padding: .55rem .5rem;
    min-width: 5.5rem;
    width: 100%;
    cursor: pointer;
}
.color-palette .d-flex .color-chip { width: auto; }
.color-palette .color-chip:hover { outline: 2px solid #212529; outline-offset: 1px; }
.color-palette .color-chip:focus-visible { outline: 3px solid #0d6efd; outline-offset: 1px; }
.color-palette .color-copied:empty { display: none; }
.color-preview {
    display: inline-block;
    padding: .25em .5em;
    border-radius: .25rem;
    font-size: .8rem;
    font-weight: 700;
    color: #fff;
    min-width: 6rem;
}
CSS);

$this->registerJs(<<<'JS'
(function () {
    document.querySelectorAll('.color-palette').forEach(function (palette) {
        var status = palette.querySelector('.color-copied');
        var timer;

        // Older copy path: plain-http hosts, or browsers that refuse the Clipboard API.
        function legacyCopy(text) {
            var ta = document.createElement('textarea');
            ta.value = text;
            ta.setAttribute('readonly', '');
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            var ok = false;
            try { ok = document.execCommand('copy'); } catch (e) { ok = false; }
            document.body.removeChild(ta);
            return ok;
        }

        function copyText(text) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(text).catch(function () {
                    if (!legacyCopy(text)) throw new Error('copy failed');
                });
            }
            return legacyCopy(text) ? Promise.resolve() : Promise.reject(new Error('copy failed'));
        }

        palette.addEventListener('click', function (e) {
            var chip = e.target.closest('[data-copy]');
            if (!chip) return;
            var hex = chip.getAttribute('data-copy');
            copyText(hex).then(function () {
                status.textContent = 'Copied ' + hex;
                status.className = 'color-copied badge bg-success';
            }, function () {
                status.textContent = 'Could not copy ' + hex;
                status.className = 'color-copied badge bg-danger';
            });
            clearTimeout(timer);
            timer = setTimeout(function () { status.textContent = ''; }, 2500);
        });

        // Live preview of the Color field, styled like a Last Time I badge.
        var input = document.getElementById(palette.getAttribute('data-color-input'));
        var preview = document.querySelector('.color-preview');
        var title = document.getElementById('item-title');
        if (input && preview) {
            var update = function () {
                preview.style.backgroundColor = input.value.trim() || 'transparent';
                preview.textContent = (title && title.value.trim()) || 'Preview';
            };
            input.addEventListener('input', update);
            if (title) title.addEventListener('input', update);
            update();
        }
    });
})();
JS);
