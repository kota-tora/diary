# 1行日記
  
#### 画像付きで1行（30文字まで）の日記を登録・編集・削除できるアプリです。

## 使用技術
- PHP 8.5 / Laravel 13
- Pest（テスト）
- Vite（フロントエンドのビルド）

## セットアップ
#### 以下を実行してください
```
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link --relative
npm install
```
## ファイル編集時
```
npm run dev 
```

## ビルド
```
npm run build
```

## テスト
```
cp .env.example .env.testing
php artisan test (全実行)
php artisan test --testsuite=Unit
php artisan test --testsuite=Integration
php artisan test --testsuite=Feature
php artisan test テストファイルパス
テストファイルパス：tests/
 ```
