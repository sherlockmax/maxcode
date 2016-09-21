# 架設

### 1.clone專案
```
git clone http://git.office.rde/max/MaxCode.git
```

### 2.安裝相依套件
```
composer install
```

### 3.更新相依套件
```
composer update
```

### 4.建立資料庫

### 5.環境設定
```
將專案目錄下"example.env"的檔案名稱修改為".env",並修改參數以對應當前環境。
```

### 6.Database　初始化　(migrate)
```
php ./artisan migrate --seed
```

### 7.遊戲設定檔
```
見最下方說明。
```

### 8.執行遊戲伺服器
```
php ./artisan schedule:run
```

### 9.完成，進入網站


# 遊戲設定檔

```
設定檔以兩種方式存取：
1.config：於config目錄中的gameset.php
2.redis：遊戲伺服器於每期開始時，向資料庫撈取並存入redis中，需要時再由redis取出

預設為使用redis方式，若redis無法取得，自動改由config取得設定。

ps.欲修改設定,若為redis則可於遊戲設定頁面(使用者必須為：max)或於資料表直接更改，新的設定會於下一期開始生效。
若為config則必須重新啟動遊戲伺服器。
```
