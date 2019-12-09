# Pagination

Pagination is enabled by default for all collections

## Disabled

Can be configured from:
* the server-side (globally or per resource)
* the client-side, via a custom GET parameter (disabled by default)

### Server-side
#### Globally
```Shell
api_platform:
    collection:
        pagination:
            enabled: false
```
#### Resource
```PHP
/**
 * @ApiResource(attributes={"pagination_enabled"=false})
 */
```

### Client-side
##### Globally
```Shell
api_platform:
    collection:
        pagination:
            client_enabled: true
            enabled_parameter_name: pagination # optional
```

In Query: `GET /foos?pagination={false|true}`

#### Specific Resource
```PHP
/**
 * @ApiResource(attributes={"pagination_client_enabled"=true})
 */
```

## Changing the Number of Items per Page

### Server-side
##### Globally
```Shell
api_platform:
    collection:
        pagination:
            items_per_page: 30 # Default value
```

##### Specific Resource
```PHP
/**
 * @ApiResource(attributes={"pagination_items_per_page"=30})
 */
```

### Client-side
##### Globally
```Shell
api_platform:
    collection:
        pagination:
            client_items_per_page: true # Disabled by default
            items_per_page_parameter_name: itemsPerPage # Default value
```

If possible to query: `GET /foos?itemsPerPage=20`

## Changing Maximum items per page

### Server-side
#### Globally
```Shell
api_platform:
    collection:
        pagination:
            maximum_items_per_page: 50
```

#### Specific Resource
```PHP
/**
 * @ApiResource(attributes={"maximum_items_per_page"=50})
 */
```

#### Specific Resource Collection Operation
```PHP
/**
 * @ApiResource(collectionOperations={"get"={maximum_items_per_page"=50}})
 */
 ```
 
 ### Partial Pagination
 The default pagination count in uery will be issued against the current requested collection.
 This may have a performance impact on really big collections.
 
 If many your are many resources with of collections, disabled the partial pagination.
 #### Globally
 ```Shell
# api/config/packages/api_platform.yaml
...
    collection:
        pagination:
            partial: true # Disabled by default
 ```
 
 #### Specific Resource
 ```PHP
 /**
  * @ApiResource(attributes={"pagination_partial"=true})
  */
 ```
 ### Client-side
 #### Globally
  ```Shell
 # api/config/packages/api_platform.yaml
 ...
     collection:
         pagination:
             client_partial: true # Disabled by default
             partial_parameter_name: 'partial' # Defaut value
  ```

Run `GET /books?partial=true`

#### SPECIFIC RESOURCE
 ```PHP
 /**
  * @ApiResource(attributes={"pagination_client_partial"=true})
  */
 ```
 
 ## Cursor based pagination
  ```PHP
  /**
   * @ApiResource(attributes={
   *   "pagination_partial"=true,
   *   "pagination_via_cursor"={"field"="id", "direction"="DESC"}
   * )
   * @ApiFilter(RangeFilter::class, properties={"id"})
   * @ApiFilter(OrderFilter::class, properties={"id"="DESC"})
   */
  ```
 
 ## Controlling the behavior of the [Doctrine](https://www.doctrine-project.org/projects/doctrine-orm/en/current/tutorials/pagination.html) ORM Paginator
 
 #### with `pagination_fetch_join_collection`
 ````PHP
 /**
 * @ApiResource(
 *     attributes={"pagination_fetch_join_collection"=false},
 *     collectionOperations={
 *         "get",
 *         "get_custom"={
 *             ...
 *             "pagination_fetch_join_collection"=true,
 *         },
 *     },
 * )
 */
 ````
  
 #### with `pagination_use_output_walkers`
 ````PHP
 /**
 * @ApiResource(
 *     attributes={"pagination_use_output_walkers"=false},
 *     collectionOperations={
 *         "get",
 *         "get_custom"={
 *             ...
 *             "pagination_use_output_walkers"=true,
 *         },
 *     },
 * )
 */
 ````
 
 ## Custom Controller Action
 
 ````PHP
 namespace App\Repository;
 
 use App\Entity\Book;
 use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
 use Doctrine\Common\Persistence\ManagerRegistry;
 use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
 use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
 use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
 use Doctrine\Common\Collections\Criteria;
 
 class BookRepository extends ServiceEntityRepository
 {
     const ITEMS_PER_PAGE = 20;
 
     private $tokenStorage;
 
     public function __construct(
         ManagerRegistry $registry,
         TokenStorageInterface $tokenStorage
     ) {
         $this->tokenStorage = $tokenStorage;
         parent::__construct($registry, Book::class);
     }
 
     public function getBooksByFavoriteAuthor(int $page = 1): Paginator
     {
         $firstResult = ($page -1) * self::ITEMS_PER_PAGE;
 
         $user = $this->tokenStorage->getToken()->getUser();
         $queryBuilder = $this->createQueryBuilder();
         $queryBuilder->select('b')
             ->from(Book::class, 'b')
             ->where('b.author = :author')
             ->setParameter('author', $user->getFavoriteAuthor()->getId())
             ->andWhere('b.publicatedOn IS NOT NULL');
 
         $criteria = Criteria::create()
             ->setFirstResult($firstResult)
             ->setMaxResults(self::ITEMS_PER_PAGE);
         $queryBuilder->addCriteria($criteria);
 
         $doctrinePaginator = new DoctrinePaginator($queryBuilder);
         $paginator = new Paginator($doctrinePaginator);
 
         return $paginator;
     }
 }
 ````
 
 Just have `return $bookRepository->getBooksByFavoriteAuthor($page);` in custom Controller
 