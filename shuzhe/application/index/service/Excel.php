<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019\5\13 0013
 * Time: 18:36
 */

namespace app\index\service;


use app\lib\exception\ExcelException;
use think\Exception;

class Excel
{
    private static $PHPExcel;
    private static $setActiveSheetIndex;
    public function __construct()
    {
    }

    /**
     * 导出报表
     * @param array $header 标题 $data = ['测试', '开发'];
     * @param array $body 内容 $data2 = [['测试', '开发']];
     * @param string $save_name 保存文件名前缀
     * @throws ExcelException
     */
    public static function export(array $header,array $body,string $format='0.00',string $save_name='术者'){
        try {
            self::$PHPExcel = new \PHPExcel();
            self::$PHPExcel->getProperties();
            self::$setActiveSheetIndex = self::$PHPExcel->setActiveSheetIndex(0);
            self::writeHeader($header);
            self::writeBody($body,$save_name,$format);
        }catch (Exception $e){
            throw new ExcelException([
                'message'=>$e->getMessage()
            ]);
        }
    }

    /**
     * 导入
     */
    public static function import(){

    }

    /**
     * 输出浏览器
     */
    public static function save(string $file_name){
        try{
            $file_name .= "_" . date("Y_m_d", time()) . ".xls";
            $file_name = iconv("utf-8", "gb2312", $file_name); // 重命名表
            header("Content-Type: text/html;charset=utf-8");
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename={$file_name}");
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter(self::$PHPExcel, 'Excel5');
            $objWriter->save('php://output'); // 文件通过浏览器下载
            exit();
        }catch (Exception $e){
            throw new ExcelException([
                'message'=>$e->getMessage()
            ]);
        }
    }

    /**
     * 写头
     * @param array $data
     * @throws ExcelException
     */
    private static function writeHeader(array $data){
        try{
            $key = ord("A"); // 设置表头
            foreach ($data as $v) {
                $column = chr($key);
                self::$setActiveSheetIndex->setCellValue($column . '1', $v);
                self::setHeaderStyle('1',$column . '1');
                $key += 1;
            }
        }catch (Exception $e){
            throw new ExcelException([
                'message'=>$e->getMessage()
            ]);
        }
    }

    /**
     * 写内容
     * @param array $data
     * @throws ExcelException
     */
    private static function writeBody(array $data,string $save_name,string $format){
        try{
            $column = 2;
//            $objActSheet = self::$PHPExcel->setActiveSheetIndex(0);
            foreach ($data as $key => $rows) { // 行写入
                $span = ord("A");
                foreach ($rows as $keyName => $value) { // 列写入
//                    if($rows[0]==$data[$key-1][0]&&in_array($keyName,[0,1,2,3,4])){
//                        $objActSheet->mergeCells(chr($span).($column-1).':'.chr($span).$column);
//                    }else {
                    self::$setActiveSheetIndex->setCellValue(chr($span) . $column, $value);
//                    }
                    self::setBodyStyle($column,chr($span) . $column,$value,$format);
                    $span++;
                }
                $column++;
            }
            self::save($save_name);
        }catch (Exception $e){
            throw new ExcelException([
                'message'=>$e->getMessage()
            ]);
        }
    }

    /**
     * 设置头部样式
     * @param string $rows
     * @param string $cell
     * @throws ExcelException
     */
    private static function setHeaderStyle(string $rows,string $cell){
        try {
            self::setRowHeight($rows);
            self::setFontName($cell);
            self::setFontSize($cell);
            self::setBold($cell);
            self::setHorizontal($cell);
            self::setWidth(substr($cell,0,1));
        }catch (Exception $e){
            throw new ExcelException([
                'message'=>$e->getMessage()
            ]);
        }
    }

    /**
     * 设置内容样式
     * @param string $rows
     * @param string $cell
     * @throws ExcelException
     */
    private static function setBodyStyle(string $rows,string $cell,$value,string $format,int $height=16){
        try {
            self::setRowHeight($rows,$height);
            self::setFontName($cell);
            self::setFontSize($cell);
            self::setHorizontal($cell);
            if(is_float($value)){
                self::setFormatCode($cell,$format);
            }
        }catch (Exception $e){
            throw new ExcelException([
                'message'=>$e->getMessage()
            ]);
        }
    }

    /**
     * 设置行高
     * @param string $rows
     * @param int $height
     * @throws ExcelException
     */
    private static function setRowHeight(string $rows,int $height=20){
        try {
            self::$setActiveSheetIndex->getRowDimension($rows)->setRowHeight($height);
        }catch (Exception $e){
            throw new ExcelException([
                'message'=>$e->getMessage()
            ]);
        }
    }

    /**
     * 设置字体
     * @param string $cell
     * @param string $font_name
     * @throws ExcelException
     */
    private static function setFontName(string $cell,string $font_name='黑体'){
        try {
            self::$setActiveSheetIndex->getStyle($cell)->getFont()->setName($font_name);
        }catch (Exception $e){
            throw new ExcelException([
                'message'=>$e->getMessage()
            ]);
        }
    }

    /**
     * 设置文字大小
     * @param string $cell
     * @param int $size
     * @throws ExcelException
     */
    private static function setFontSize(string $cell,int $size=12){
        try {
            self::$setActiveSheetIndex->getStyle($cell)->getFont()->setSize($size);
        }catch (Exception $e){
            throw new ExcelException([
                'message'=>$e->getMessage()
            ]);
        }
    }

    /**
     * 设置文字加粗
     * @param string $cell
     * @throws ExcelException
     */
    private static function setBold(string $cell){
        try {
            self::$setActiveSheetIndex->getStyle($cell)->getFont()->setBold(true);
        }catch (Exception $e){
            throw new ExcelException([
                'message'=>$e->getMessage()
            ]);
        }
    }

    /**
     * 设置文字居中
     * @param string $cell
     * @throws ExcelException
     */
    private static function setHorizontal(string $cell){
        try {
            self::$setActiveSheetIndex->getStyle($cell)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            self::$setActiveSheetIndex->getStyle($cell)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        }catch (Exception $e){
            throw new ExcelException([
                'message'=>$e->getMessage()
            ]);
        }
    }

    /**
     * 设置列宽度
     * @param string $column
     * @param int $width
     * @throws ExcelException
     */
    private static function setWidth(string $column,int $width=20){
        try {
            self::$setActiveSheetIndex->getColumnDimension($column)->setWidth($width);
        }catch (Exception $e){
            throw new ExcelException([
                'message'=>$e->getMessage()
            ]);
        }
    }

    /**
     * 设置列或者单元格格式
     * @param string $column
     * @param string $format
     * @throws ExcelException
     */
    private static function setFormatCode(string $column,string $format){
        try {
            self::$setActiveSheetIndex->getStyle($column)->getNumberFormat()->setFormatCode($format);
        }catch (Exception $e){
            throw new ExcelException([
                'message'=>$e->getMessage()
            ]);
        }
    }
}