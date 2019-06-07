<?php
namespace developerTest;

\Bitrix\Main\Loader::includeModule('iblock');

class IB
{

    /**
     * Основной метод класса
     * @param  boolean $ibId   ID инфоблока
     * @param  array   $params входные параметры
     * @param  boolean $raw    декорировать вывод
     * @return array           данные из инфоблока
     */
    static public function get($ibId, $params = [], $raw = true)
    {
        
        $list = [];

        $cache = \Bitrix\Main\Application::getInstance()->getCache();
        $taggedCache = \Bitrix\Main\Application::getInstance()->getTaggedCache();

        $cacheTtl = CACHE_TIME;

        $key = self::generateKey($params);

        $cacheId = 'DeveloperTest_get_'.$key;
        $cachePath = '/DeveloperTest/IB/get/'.$key;
       

        if ($cache->initCache($cacheTtl, $cacheId, $cachePath)) {
            $list = $cache->getVars();
        } else {
            
            /**
             * здесь нужно объяснить почему в общем случае лучше для начала выгрести ID
             * при большом, действительно большом числе свойств их получение при первичном создании кэша может быть фатально;
             * я с этим столкнулся, было неприятно, вот хотел похвастаться решением
             */

            $db = \CIBlockElement::GetList(
                is_array($params['order']) ? $params['order'] : [],
                array_merge(['IBLOCK_ID' => $ibId], is_array($params['filter']) ? $params['filter'] : []),
                false, 
                is_array($params['limit']) ? $params['limit'] : [],
                [
                    'IBLOCK_ID',
                    'ID',
                ]
            );
            
            $ids = [];

            while( $id = $db->Fetch() ){

                $ids[] = $id['ID'];

            }

            if (count($ids)>0):
                
                $db = \CIBlockElement::GetList(
                    is_array($params['order']) ? $params['order'] : [],
                    [
                        'IBLOCK_ID' => $ibId,
                        'ID'        => $ids
                    ],
                    false, 
                    is_array($params['limit']) ? $params['limit'] : [],
                    is_array($params['select']) ? $params['select'] : []
                );
              
                while( $item = $db->Fetch() ){

                    $list[] = $item;

                }

            endif;//count($ids)>0



            $taggedCache->startTagCache($cachePath);
            $taggedCache->registerTag('DeveloperTest');
            $taggedCache->endTagCache();

            $cache->startDataCache();
            $cache->endDataCache($list);

        }

        return $raw ? $list : self::decorator($list);

    }

    /**
     * так бывает очень удобно делать когда это нужно отдать апи
     * @param  array $data  данные для декорирования
     * @return array        отдекорированные данные
     */
    static public function decorator($data)
    {
        
        return $data;
    
    }

    /**
     * генерация ключа по параметрам
     * @param  array  $params параметры
     * @return string         ключ
     */
    static private function generateKey($params = [])
    {

        array_walk_recursive($params, function ($item, $key) use (&$result) {
            $result[] = $item;    
        });

        return md5(SITE_ID.implode( '_', $result ) );

    }

    /**
     * сброс кэша
     * @param  string $tag тег для сброса
     */
    static public function clearCache($tag)
    {

        $GLOBALS['CACHE_MANAGER']->ClearByTag($tag);

    }

    /**
     * получить все без фильтра
     * этот и ниже методы в рамках класса имеют смысл если под каждый инфоблок мы пишем свой класс
     * выбираемые свойства тоже предустановлены
     * $ibId не имеет смысла и тоже предустановлен в get 
     * я оставлю их как демонстрацию идеи
     * @param  boolean $raw декорирование данных
     * @return array        Элементы
     */
    static public function getAll($ibId, $raw = false)
    {
        
        return self::get($ibId, [], $raw);

    }

    /**
     * получить элемент по симвокоду
     * @param  int     $ibId ID инфоблока
     * @param  string  $code символьный код
     * @param  boolean $raw  декорировать или нет
     * @return array         данные по элементу
     */
    static public function getBySymbolCode($ibId, $code, $raw = false)
    {
        
        $params = [
            'filter' => ['=CODE'=>$code],
            'limit'  => ['nTopCount'=>1]
        ];
        
        $list = self::get($ibId, $params, $raw);
        
        return $list[0];

    }


    /**
     * получить элемент по ID
     * @param  int     $ibId ID инфоблока
     * @param  int     $ID   ID элемента
     * @param  boolean $raw  декорировать или нет
     * @return array         данные по элементу
     */
    static public function getById($ibId, $ID, $raw = false)
    {
        
        $params = [
            'filter' => ['=ID'=>$ID],
            'limit'  => ['nTopCount'=>1]
        ];

        $list = self::get($ibId, $params, $raw);
        
        return $list[0];

    }

}
