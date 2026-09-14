<?php
require_once('admin/db/config.php');
// fetch active partners
$partners = [];
$stmtFetchPartner = $db->prepare("SELECT * FROM partners WHERE status = 1");
$stmtFetchPartner->execute();
$partners = $stmtFetchPartner->get_result()->fetch_all(MYSQLI_ASSOC);

$fallbackLogos = [
    'images/company-supports-logo-1.svg',
    'images/company-supports-logo-2.svg',
    'images/company-supports-logo-3.svg',
    'images/company-supports-logo-4.svg',
];
?>

<div class="company-supports-slider">
    <div class="swiper">
        <div class="swiper-wrapper">
            <?php if (!empty($partners)): ?>
                <?php foreach ($partners as $item): ?>
                    <?php
                    $imagePath = 'admin/' . $item['image'];
                    if (!file_exists($imagePath)) {
                        $fallbackIndex = ($item['idpartner'] - 1) % count($fallbackLogos);
                        $imagePath = $fallbackLogos[$fallbackIndex];
                    }
                    ?>
                    <div class="swiper-slide">
                        <div class="company-supports-logo">
                            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Partner Logo">
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?php foreach ($fallbackLogos as $logo): ?>
                    <div class="swiper-slide">
                        <div class="company-supports-logo">
                            <img src="<?php echo htmlspecialchars($logo); ?>" alt="Partner Logo">
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
    </div>
    </div>
</div>   </div>
    </div>
</div>