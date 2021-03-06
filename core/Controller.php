<?php

namespace Endocore\Core;

use Endocore\App\Configs\AppConfig;


class Controller
{

    public function __construct()
    {
        //include the init config file
        // include olmalı ki her seferinde config dosyası yüklenebilsin
        $configs = include(AppConfig::INIT_CONFIG_FILE);

        // Load the helpers which is autoloaded
        if ($configs['helpers']) {
            foreach ($configs['helpers'] as $data => $key) {
                if (file_exists($file = AppConfig::HELPERS_DIR . AppConfig::DS . $key . '.php')) {
                    include_once($file);
                }
            }
        }

        // Load the libraries which is autoloaded
        if ($configs['libraries']) {
            foreach ($configs['libraries'] as $data => $key) {
                if (file_exists($file = AppConfig::LIBRARIES_DIR . AppConfig::DS . $key . '.php')) {
                    $class = '\\Libraries\\' . ucfirst($key);
                    if (class_exists($class)) {
                        $this->{$key} = new $class();
                    }
                }
            }
        }
    }


    /**
     * @param $file
     * @param array $params
     * @throws \Exception
     */
    public function render(string $file, array $params = []): void
    {
        View::render($file, $params);
    }

    /**
     * Partial View dosyası çağırmamıza yarayan metod
     * @param string $file dosyasını adını alır
     * @param array $params parametreleri alır
     * @return void view sınıfından render metodu döner
     * @throws \Exception
     */
    public function partialRender(string $file, array $params = []): void
    {
        View::partialRender($file, $params);
    }

    /**
     * Model dizininden model dosyası çağırır
     * @param string $model model dosyası adı
     * @return void model sınıfı
     */
    public function model(string $model): \stdClass
    {
        /**
         * Eğer model dosyası varsa
         * çağırıp döndürelim
         */
        if (file_exists($file = AppConfig::MDIR . AppConfig::DS . ucfirst($model) . ".php")) {
            require_once $file;
            /**
             * Eğer model sınıfı tanımlıysa
             * model sınıfını döndür
             */
            $class = '\\App\\Models\\' . ucfirst($model);
            if (class_exists($class)) {
                return new $class;
                /**
                 * Model sınıfı tanımlı değilse programı durdur
                 */
            } else {
                exit("Model dosyasında sınıf tanımlı değil: $class");
            }
            /**
             * Eğer sınıf yoksa, hata döndürelim
             */
        } else {
            exit("Model dosyası bulunamadı: {$model}.php");
        }
    }


}
