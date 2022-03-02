# API

## Requests
輸入如下：
- 來源幣別 (source_currency)
- 目標幣別 (target_currency)
- 金額數字 (source_amount)

輸出見底下 Responses 區塊

### **GET** - /api/transferred-amount

#### CURL 範例

```sh
curl -X GET "http://base-url/api/transferred-amount?source_currency=TWD&target_currency=USD&source_amount=1122233213" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

#### Query Parameters

- **source_currency** 格式如下

```
{
  "type": "string",
  "enum": [
    "TWD" or "JPY" or "USD"
  ]
}
```
- **target_currency** 格式如下

```
{
  "type": "string",
  "enum": [
    "USD" or "JPY" or "USD"
  ]
}
```
- **source_amount** 格式如下

```
{
  "type": "string",
  "enum": [
    "1122233213" (需為 numeric, 最小為 0)
  ]
}
```

#### Header Parameters

- **Content-Type** should respect the following schema:

```
{
  "type": "string",
  "enum": [
    "application/json"
  ],
  "default": "application/json"
}
```
- **Accept** should respect the following schema:

```
{
  "type": "string",
  "enum": [
    "application/json"
  ],
  "default": "application/json"
}
```

## Responses
```json
{
  "data": {
    "target_currency": "USD",
    "source_currency": "TWD",
    "source_amount": "1122233213",
    "transferred_amount": "36,820,471.72"
  }
}
```

# 測試
已針對各種幣別轉換寫測試, 以及三種輸入驗證測試, 執行 `php artisan test`