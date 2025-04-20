# php-mubi

]php-mubi is a playful PHP CLI tool that lets you explore your movie-watching habits on MUBI. It’s not meant to be a serious data tool, but rather something fun to bring up when chatting about movies with friends.

⚠️ Note: This is not intended as a production-grade or heavily-tested package. It's just a fun side project for movie lovers.

🎬 What Can It Do?

Once you run the tool, you’ll be prompted with options like:
Possible actions:
1. Search movies by country
2. Search movies by director
3. Search movies by genre
4. Get stats
   
If you pick 4, you’ll see:
Possible stats:
1. Top directors
2. Top countries
3. Top genres
4. Rating by movie duration

🛠 Installation

You can install it using Composer:

`composer require drahil/php-mubi:dev-main`

🚀 Usage

Just run the CLI script:

`vendor/bin/mubi-stats.php`

You’ll be prompted to paste the URL of your public MUBI profile (e.g., https://mubi.com/en/users/your-mubi-id). The tool will take it from there—no need to export anything manually from MUBI.


