## Simple Search API for Bitenow

> This project is more like getting things done fast. The primary goal is to provide simple search API for the main repository of Bitenow.

Simple search that allows users to search for businesses in a specific location. The search will return a list of businesses that match the search criteria. The search will also allow users to filter the search results based on various criteria such as distance, rating, and categories.

### Query Parameters

-   `filter[term]` (string) - The search term (e.g. "food", "restaurants"). If term isn't included, the endpoint will default to searching across businesses based on the provided location and popularity.
-   `filter[location]` (string) - The search location (e.g. "San Francisco", "CA"). This string must be a city or a zip code. The location "current location" is not supported.
-   `filter[latitude]` (decimal) - Required, if location is not provided. Latitude of the location to search from. If latitude is provided, longitude is required too.
-   `filter[longitude]` (decimal) - Required if location is not provided. Longitude of the location to search from. If longitude is provided, latitude is required too.
-   `filter[radius]` (int) - Optional. Search radius in meters. If the value is too large, an error may be returned. The max value is 40_000 meters (25 miles).
-   `filter[cuisines]` (string) - Optional. Cuisines to filter the search results with. If multiple cuisines are specified, they must be comma-separated.
-   `filter[transactions]` (string) - Optional. Transaction types to filter the search results with. If multiple transactions are specified, they must be comma-separated. For example, "delivery, pickup".
-   `filter[price]` (string) - Optional. Pricing levels to filter the search results with. The price can be a number between 1 and 4. The price filter can be a range, separated by a comma (e.g. "1,2,3").
-   `filter[open_now]` (bool) - Optional. Default to false. When set to true, only return the businesses open now.
-   `filter[open_at]` (int) - Optional. An integer represending the Unix time in the same timezone of the search location. If specified, it will return business open at the given time. For example, to search for businesses open at 6pm in San Francisco on a Sunday, the value should be 1478616000.
-   `sort_by` (string) - Optional. Sort the results by one of the these modes: rating, review_count or distance. By default it's automatically set to popularity.
-   `include` (string) - Optional. Additional information to return. Separate multiple values with commas (e.g. "cuisines,reviews,reviews.author,openingHours").
-   `paginate` (int) - Optional. Number of business results to return. By default, it will return 20. Maximum is 50.
-   `page` (int) - Optional. Display the page number of the results. This will be used to calculate the `offset` value.

### Run on Local

The fastest way to run the project is to use **Herd**. You can run the project by running the following command:

1. Download and install Herd from [Herd](https://herd.laravel.com)
2. Navigate to Herd directory `cd ~/herd`
3. Clone this repository `git clone`
4. Run `cd bitenow-api` and run `composer install`
5. Copy the `.env.example` file to `.env` and update the database credentials
6. Run `php artisan migrate --seed` to migrate the database and seed the data
