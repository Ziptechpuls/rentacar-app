# 管理者接続情報

## 🌐 アクセス URL

### ローカル開発環境

- **ベース URL**: `http://localhost:8080`
- **管理者ログイン**: `http://[サブドメイン].localhost:8080/admin/login`

## 🏢 サブドメイン別管理者情報

### 1. rentcar サブドメイン（テナント管理者）

- **URL**: `http://rentacar.localhost:8080/admin/login`
- **会社名**: レンタカー株式会社
- **管理者アカウント**:
  - `rentacar.admin@example.com` / `password` ✅ **推奨**
  - `admin@example.com` / `password`
  - `rentacar.admin2@example.com` / `password`
  - `test1234@gmail.com` / `password`
  - `test12345@gmail.com` / `password`

### 2. admin サブドメイン（スーパー管理者）

- **URL**: `http://admin.localhost:8080/admin/login`
- **会社名**: システム管理
- **管理者アカウント**:
  - `super.admin@system.local` / `password` ✅ **推奨**
  - `super@example.com` / `password`

### 3. test サブドメイン（テナント管理者）

- **URL**: `http://test.localhost:8080/admin/login` ⚠️ **ブラウザ問題あり**
- **会社名**: テストレンタカー
- **管理者アカウント**:
  - `test.admin@example.com` / `password`
  - `test.admin2@example.com` / `password`

## 🛠️ 技術情報

### データベース接続

- **DB 種別**: PostgreSQL (Supabase)
- **ホスト**: `aws-0-ap-northeast-1.pooler.supabase.com`
- **ポート**: `6543`
- **データベース**: `postgres`
- **SSL**: 有効

### Docker 環境

```bash
# 環境起動
docker-compose up -d

# 管理者確認
docker-compose exec app php artisan tinker --execute="App\Models\Admin::with('company')->get()"

# ログ確認
docker-compose logs app
```

## 🔧 トラブルシューティング

### ブラウザリダイレクト問題（test サブドメイン）

- **症状**: `http://test.localhost:8080/admin/login` → `http://test.localhost:8080/` にリダイレクト
- **対処**: `rentcar`サブドメインを使用（完全動作）
- **原因**: ブラウザキャッシュ/環境固有問題

### CSS/JS 読み込み問題

- **対処済み**: `APP_URL=http://localhost:8080` 設定済み
- **アセット URL**: 正常に `localhost:8080` から配信

## ✅ 動作確認済み機能（rentcar サブドメイン）

- ✅ 管理者ログイン/ログアウト
- ✅ ダッシュボード表示
- ✅ 車両管理（作成/編集/削除）
- ✅ 車両タイプ別料金設定
- ✅ 予約管理
- ✅ CSS/JS アセット読み込み

## 📝 推奨開発フロー

1. **rentcar サブドメイン**で管理者機能を開発・テスト
2. **admin サブドメイン**でスーパー管理者機能を開発・テスト
3. 本番環境では各テナントの実際のサブドメインを使用

---

**最終更新**: 2025 年 1 月 9 日  
**動作確認環境**: macOS, Docker Desktop, Chrome/Safari
