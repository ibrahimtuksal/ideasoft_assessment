
# İbrahim Tuksal Ideasoft Take-Home Assesment




## Bilgisayarınızda Çalıştırın

Projeyi klonlayın

```bash
  git clone https://github.com/ibrahimtuksal/ideasoft_assessment.git
```

Proje dizinine gidin

```bash
  cd my-project
```

Gerekli paketleri yükleyin

```bash
  composer install
```

Sunucuyu **symfony** ile çalıştırın 

```bash
  symfony serve
```

**.env** dosyasından veritabanı ayarlarınızı yapın


**Sql** sorgularını alın ve veritabanında sorguları çalıştırın

```bash
  php bin/console d:s:u --dump-sql
```

**Doctrine Fixtures**'i çalıştırın

```bash
  php bin/console doctrine:fixtures:load
```
  
## API Kullanımı

#### Sipariş Ekle

```http
  POST /order/add
```

| Parametre | Tip     | Açıklama                |
| :-------- | :------- | :------------------------- |
| `customer` | `integer` | Müşteri id |
| `items` | `array` | Dizi olarak çoklu ürün alır |
| `productId ` | `integer` | Ürün id |
| `quantity ` | `integer` | Ürün miktarı |

#### Örnek json
```javascript
{
  "customer": 5,
  "items": [
          {
              "productId": 1,
              "quantity": 1
          },
          {
              "productId": 2,
              "quantity": 4
          }
      ]
}
```


#### Sipariş Sil

```http
  GET /order/delete/${order}
```

| Parametre | Tip     | Açıklama                       |
| :-------- | :------- | :-------------------------------- |
| `order`      | `integer` | Sipariş id |

#### Örnek Response
```javascript
{
    "success": true,
    "message": "ORDER_DELETED",
    "response": {
        "id": 1,
        "createdAt": "2022-09-08 19:50"
    }
}
```


#### Sipariş Liste

```http
  GET /order/list/${order}
```

| Parametre | Tip     | Açıklama                       |
| :-------- | :------- | :-------------------------------- |
| `order`      | `integer` | Sipariş id |

#### Örnek Response
```javascript
{
    "id": 1,
    "createdAt": "2022-09-08 20:09",
    "customer": {
        "id": 1,
        "name": "Türker Jöntürk"
    },
    "items": [
        {
            "product": "Reko Mini Tamir Hassas Tornavida Seti 32'li",
            "unitPrice": "49.50",
            "quantity": 3,
            "total": 148.5
        },
        {
            "product": "Legrand Salbei Anahtar, Alüminyum",
            "unitPrice": "22.80",
            "quantity": 8,
            "total": 182.4
        }
    ],
    "totalPrice": "330.9",
    "discountPrice": "258.6"
}
```
  
## Kullanılan Teknolojiler

**Framework:** Symfony5

**Genel Olarak Paketler:** symfony/validator, nelmio/api-doc-bundle, doctrine/orm

  