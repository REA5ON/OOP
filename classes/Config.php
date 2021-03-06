<?php


/*
    1. Основная задача компонента Config - работа с массивами конфигураций.
    2. Чтобы работать с вложеными массивами нужно создать функцию get(),
       где на вход принимаем строку состоящую из ключей массива.
       Для удобства записи разделяем ключи "." функцией explode().
       Далее проходимся циклом по ключам - каждое полученое значение из массива перезаписываем в
       переменную $config до тех пор пока не дойдем до последнего ключа. Последняя итерация
       цикла будет содержать нужное нам значение.
    3. Максимальная вложенность конфиг файла, с которой может работать компонент Config - неограниченно.

*/


class Config {

    public static function get($path = null) {
        //если не пустой $path
        if (($path)) {
            //записываем глобальную переменную в $config
            $config = $GLOBALS['config'];

            /*
            explode - берем строчку полученую из $path и между ключей в массиве ставим точки
            Перезаписываем переменную $path
            делаем обращение к нумерованым элементам массива так adafd.dgsdg.dhhd а не так sfsf => fvfsg => []
            */

            $path = explode('.', $path);


            foreach ($path as $item) {
                /*
                Перебираем массив по ключам полученым с $path
                Закидываем в цикл и если есть то распаковуем до тех пор вложенные массивы,
                пока не найдем нужное нужное нам значение
                */
                if (isset($config[$item])) {
                    //Перезаписываем $config каждой итерацией до тех пор пока не дойдем до значения
                    $config = $config[$item];
                }
            }

            //Перезаписываем полученый $config

            return $config;
        }
        //возвращаем false если $path пустой
        return false;
    }
}
