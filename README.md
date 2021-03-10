# Модуль BRANDS #

1. Структура модуля:

```
Brands
---Controllers
------BrandsController.php
---Middlewares
------CheckAdmin.php
---Migrations
------2021_03_04_135914_create_vendors_table.php
---Models
------ShopVendors.php
---Providers
------BrandsServiceProvider.php
---Repositories
------VendorRepository.php
---Requests
------StorePostRequest.php
---Routes
------web.php
---Views
------add.blade.php
------index.blade.php
```
2. Файл add.blade.php использован для представления функционала создания и редактирования брэнда
    
    2.1 Структура `input` имеет вид формата `name="vendor_name` [**name обязательно совпадает с именем поля в бд**]
    ```html
       <input name="vendor_name"
        type="text" 
        value="@isset($info->vendor_name)
                 {{$info->vendor_name}}
               @endisset"> 
    ```
   2.2 Значение `value` должно заполняться соответствующим полем из коллекции редактируемого бренда и обязательно должно
   проверяться существует ли переменная - `@isset($info->vendor_name)`
   

3. Отправленный запрос на создание или редактирование брэнда проходит через валидатор *StorePostRequest.php*
    со следующими правилами:
   ```php
   public function rules()
    {
        return [
            'vendor_name' => 'required|string|max:150',
            'vendor_url' => 'required|unique:shop_vendors,vendor_url,'.$this->id.',vendor_id|max:150',
            'vendor_letter' => 'required|string|max:1',
        ];
    }
   ```
   Запись типа `'vendor_url' => 'required|unique:shop_vendors,vendor_url,'.$this->id.',vendor_id|max:150',` реализует 
   логику типа vendor_url - обязательный, до 150 символов, **уникальный в таблице `shop_vendors`, в колонке `vendor_url` 
   кроме записи `vendor_id = $this->id`**
   Это позволяет использовать один валидатор для двух разных запросов (при добавлении $this->id) пустой, соответственно
   исключений не будет.
   
   
4. Функция добавления нового брэнда в базу данных выглядит так:
```php
public function new_vendor($request)
{
    $vendor = ShopVendors::create($request);
    return $vendor;
}
```
Благодаря тому что ``name`` полей для заполнения содержит такие же имена, как и поля в базе данных
функция добавления может быть реализована в одну строку.

5. Функция редактирования может быдт реализована 2-мя способами:

Первый способ (это способ я использовал у сбея):
```php
public function edit_vendor($request, $vendor_id)
{
    $vendor = ShopVendors::find($vendor_id);
    $vendor->vendor_id
        ? $vendor->fill($request)->save()
        : $vendor = "Нет брэнда с тикам идентификатором";

    return $vendor;
}
```
Второй способ:
```php
public function edit_vendor($request, $vendor_id)
{
    $vendor = ShopVendors::where('vendor_id',$vendor_id)->update($request);
    return $vendor;
}
```
Оба эти способа позволяют отслеживать ошибки и в случае их возникновения возвращают результат ошибки
Но в случае если ошибок не возникло и обновление записи произошло успешно, в первом случае функция переменная 
```$vendor``` содержит значение `true`, а во втором она равна значению `null`.

5. Получение списка брендом реализованов в функции: 
```php
public function get_vendors($request, bool $paginate = false, int $per_page = 0)
{
    $vendors = ShopVendors::query();

    /** Тут к запросу добавляются дополнительные параметры*/
    isset($request->vendor_status) ? $vendors->orderBy('vendor_id', 'DESC')
                                     : $vendors->orderBy('vendor_id','ASC');

    isset($request->vendor_name) ? $vendors->where('vendor_name', 'LIKE', $request->vendor_name.'%')
                                 :null;
    isset($request->vendor_status) ? $vendors->where('vendor_name','=', $request->vendor_status)
                                 :null;

    /**Тут идет проверка пагинировать данные или нет*/
    $paginate
        ? $per_page  
            ? $result = $vendors->paginate($per_page)
            : $result = 'Укажите количество объектов на странице'  
        : $result = $vendors->get();
    
    return $result;
}
```
Запись `$vendors = ShopVendors::query();` создает `$vendor` являющийся экземпляром конструктора запроса, это позволяет модифицировать запрос в зависимости от параметров фильтрации или сортировки.

6. Удаление происходит с помощью функции:
```php
public function delete_vendor($id)
{
  $vendor = ShopVendors::where('vendor_id', $id)->first();
  $vendor->vendor_id ? $result = $vendor->delete() : $result = 'Нет бренда с таким идентификатором';
  return $result;
} 
```
