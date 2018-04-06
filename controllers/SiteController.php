<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{


    public function beforeAction($action)
    {

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Credentials: true");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Max-Age: 1000');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
        header('Access-Control-Request-Headers: X-PINGOTHER, Content-Type');
        header('Content-Type: text/plain');
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [],
                'actions' => [
                    'incoming' => [
                        'Origin' => ['*'],
                        'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                        'Access-Control-Request-Headers' => ['*'],
                        'Access-Control-Allow-Credentials' => null,
                        'Access-Control-Max-Age' => 86400,
                        'Access-Control-Expose-Headers' => [],
                    ],
                ],
            ],
        ];


    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }



    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }



    /* Process request form client and save image file.
     * Returns JSON
     */
    public function actionUploadimage() {
        $request_body = Yii::$app->request->getRawBody();
        $request = json_decode($request_body, true);
        $base64_image_string = isset($request['image']) ? $request['image'] : 'Not isset';
        $file = $this->save_base64_image($base64_image_string);

        if ($file) {
            return json_encode(array(
                'status' => 200,
                'url' => Url::base(true) . '/uploads/' . $file
                )
            );
        }
        die(json_encode(array('status' => 500)));
    }



    /* Convert Base64 string to image file and save it.
     * @return String (filename)
     */
    function save_base64_image($base64_image_string) {
        $splited = explode(',', substr( $base64_image_string , 5 ) , 2);
        $mime = $splited[0];
        $data = $splited[1];
        $mime_split_without_base64 = explode(';', $mime,2);
        $mime_split = explode('/', $mime_split_without_base64[0],2);
        if(count($mime_split) == 2) {
            $extension = $mime_split[1];
            if($extension == 'jpeg')$extension = 'jpg';
            $filename = bin2hex(openssl_random_pseudo_bytes(16));
            $output_file = $filename . '.' . $extension;
        } else return false;
        file_put_contents( "uploads/" . $output_file, base64_decode($data) );
        return $output_file;
    }


    /**Read json file and return JSON object
     * Returns JSON
     */
    public function actionTickets () {
        $file = 'asset/tickets.json';
        if (file_exists($file)) {
            $str = file_get_contents(Url::base(true) . '/asset/tickets.json');
            $json_tickets = json_decode($str, true);
            die(json_encode(array(
                'status' => 200,
                'tickets' => $json_tickets
            )));
        }
        die(json_encode(array('status' => 500)));
    }



}
