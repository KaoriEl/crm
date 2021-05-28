<?php


namespace App\Http\Controllers\Excel\Service;


use App\Http\Controllers\Excel\WorkWithExcel;
use Carbon\Carbon;

class CreateExcel
{

    private $excel;
    private static $instance;

    public function __construct()
    {
        $this->excel = new WorkWithExcel();
    }

    /**
     * Создание файла Excel на диске, который возвращает объект файла
     * TODO: устарело, заказчику не надо
     */
    public function create() {
        $service = $this->excel->getService();
        $time = Carbon::now()->setTimezone('Europe/Moscow')->format('d.m.Y H:i');

        $spreadsheet = new \Google_Service_Sheets_Spreadsheet([
            'properties' => [
                'title' => 'Выгрузка за ' . $time
            ]
        ]);

        $spreadsheet = $service->spreadsheets->create($spreadsheet, [
            'fields' => 'spreadsheetId'
        ]);

//        $this->insertPermission($service, $spreadsheet->spreadsheetId, null, 'anyone', 'writer');

        return $spreadsheet;
    }

    /**
     * Возвращение объекта при инициализации
     * @return CreateExcel
     */
    public static function getInstance() {
        if(empty(self::$instance)) {
            self::$instance = new CreateExcel();
        }
        return self::$instance;
    }

//    private function insertPermission($service, $fileId, $value, $type, $role)
//    {
//        $client = $this->excel->getClient();
//        $service = new \Google_Service_Drive($client);
//
//        $canViewAnyonePermission = new \Google_Service_Drive_Permission([
//            'type' => 'anyone',
//            'role' => 'reader',
//        ]);
//
//        $service->permissions->create($fileId, $canViewAnyonePermission, ['fields' => 'id']);
//
//    }


}
