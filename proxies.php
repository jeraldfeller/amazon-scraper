<?php

function readProxies($locale, $delimiter = ',')
{
    $file = "./proxies/{$locale}.txt";
    if (!file_exists($file)) {
        file_put_contents($file, '');
    }

    $data = file_get_contents($file);
//    $proxy = explode($delimiter, $data);

    return $data;
}

function writeProxies($data, $locale)
{
    $file = "./proxies/{$locale}.txt";
    return file_put_contents($file, $data);
}

$proxy_list = ['it', 'de', 'uk', 'fr', 'es'];
$errors = [];
$success = '';
if (isset($_POST['save'])) {
    foreach ($proxy_list as $locale) {
        $data = filter_input(INPUT_POST, $locale, FILTER_SANITIZE_STRING);
        $data = trim($data);
        if (!writeProxies($data, $locale)) {
            if ($data !== '') {
                $errors[] = sprintf("Locale %s couldn't be saved", $locale);
            }

        }
    }
    if (count($errors) === 0) {
        $success = 'Proxies saved';
    }
}

include 'header.php';
?>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>Edit proxies</h1>
                <p>The save buttons, save the configuration for all proxies. So you can edit all textboxes at once and click save only once.</p>
            </div>
            <?php if (count($errors) > 0): ?>
                <div class="col-12">
                    <?php foreach ($errors as $msg): ?>
                        <div class="alert alert-danger"><?php echo $msg; ?></div>
                    <?php endforeach; ?>

                </div>
            <?php
            endif; ?>
            <?php if ($success !== ''): ?>
                <div class="col-12">
                    <div class="alert alert-success"><?php echo $success; ?></div>
                </div>
            <?php endif; ?>
            <div class="col-12">
                <form class="form" method="post">
                    <input type="hidden" name="save" value="1">
                    <?php foreach ($proxy_list as $locale):
                        $data = readProxies($locale);
                        ?>
                        <div class="form-group">
                            <h2><?php echo strtoupper($locale); ?></h2>
                            <textarea rows="10" class="form-control" name="<?php echo $locale; ?>"><?php echo $data; ?></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-info">Save</button>
                        </div>
                    <?php endforeach; ?>

                </form>
            </div>
        </div>
    </div>
<?php
function getProxy($location)
{
    error_reporting(E_ALL);
    ini_set('display_errors',1);
    define('ROOT', '/var/www/html/am/amazon-scraper/');

    $data = '';
    switch ($location) {
        case 'it':
            $data = file_get_contents(ROOT . '/proxies/it.txt');
            break;
        case 'uk':
            $data = file_get_contents(ROOT . '/proxies/uk.txt');
            break;
        case 'fr':
            $data = file_get_contents(ROOT . '/proxies/fr.txt');
            break;
        case 'de':
            $data = file_get_contents(ROOT . '/proxies/de.txt');
            break;
        case 'es':
            $data = file_get_contents(ROOT . '/proxies/es.txt');
            print_r($data);
            break;
    }

    $delimiter = (strstr($data, ',') !== false) ? ',' : "\n";
    $proxy = explode($delimiter, $data);

    return $proxy[mt_rand(0, count($proxy) - 1)];
}

echo 'test =' . getProxy('it');
include 'footer.php';
