# Installation #

### install by composer ###
<pre><code>
composer require virtualorz/tag
</code></pre>

### edit config/app.php ###
<pre><code>
'providers' => [
    ...
    Virtualorz\Tag\TagServiceProvider::class,
    Virtualorz\Pagination\PaginationServiceProvider::class,
    ...
]

'aliases' => [
    ...
    'Tag' => Virtualorz\Tag\TagFacade::class,
    'Pagination' => Virtualorz\Pagination\PaginationFacade::class,
    ...
]
</code></pre>

### migration db table ###
<pre><code>
php artisan migrate
</code></pre>
# usage #
#### 1. get cate list data ####
<pre><code>
$dataSet = Tag::list($page=0,$is_backend=0);
</code></pre>
$page : page for data display,

＄is_backend : if 0 then display disabled data, if 1 then display enable data only
$dataSet : return date  
$_GET['keyword'] : search for name keyword

#### 2. add data to cate ####
<pre><code>
Tag::add();
</code></pre>
with request variable name required : tag-name,tag-enable

#### 3. get cate detail ####
<pre><code>
$dataRow = Tag::detail($tag_id);
</code></pre>

#### 4. edit data to cate ####
<pre><code>
Tag::edit();
</code></pre>
with request variable name required : tag-name,tag-enable

#### 5. delete cate data ####
<pre><code>
Tag::delete();
</code></pre>
with request variable name required : id as integer or id as array

#### 6. enable cate data ####
<pre><code>
Tag::enable($type);
</code></pre>
with request variable name required : id as integer or id as array
$type is 0 or1 , 0 to disable i to enable




