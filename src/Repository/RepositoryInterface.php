<?php
/**
 * Repository slouží jako sklad objektů.
 *
 * Eric Evans DDD:
 * Agregát je "shluk" (cluster) objektů, který používáme jako nedělitelnou jednotku pro změny dat.
 * Každý agregát má kořen a rozsah. Rozsah definuje co se nechází uvnitř, v agregátu. Kořen je jedinečný, specifický objekt z agregátu.
 * Kořen je jediný objekt z agregátu, na který je možno odkazovat z nějakého vnějšího objektu, tedy jediný objekt na který může nějaký
 * objekt mimo agregát držet referenci. Kořen agregátu jediným objektem, který je vracen metodami repository.
 *
 * Svoboda:
 * Protože kořen je jedinečný jedná se tedy o entitu.
 * Referencí na agregát je jeho kořen, z toho vyplývá, že agregát je strom. Strom agregátu je definován kořenem a rozsahem.
 * Jedná se o částečný podstrom domény, kořen agregátu je kořenem podstromu, ale nejedná se o celý podstrom, tento podstorm má jen hloubku
 * definovanou rozsahem.  To, který uzel stromu objektů celé domény bude kořenem agregátu a to, jaký bude rozsah agregátu
 * je dáno požadavky na změny dat objektů v agregátu s ohledem na nutné zachování konzistence dat. Agregát obsahuje jen objekty od kořene agregátu
 * po danou hloubku stromu agregátu a jenom tyto objekty mohou být dotčeny změnou dat.
 * Úplný agregát obsahuje všechny existující objekty, členy agregátu ve stávající doméně. Pokud aplikuji ještě výběrové
 * kritérium, získám podagregát, ten obsahuje jen vybrabé větve agregátu a získám ho prohledáváním do šířky.
 * Kořen agregátu je jedinou referencí agregátu a tak pouze průchodem agregátu od kořene mohu měnit data členů agregátu, jinak poškodím konzistenci dat.
 * Proto je kořen agregátu jediným objektem, který je vracen metodami repository. Samozřejmě reository může vracet jeden kořen nebo kolekci kořenů.
 * Rozsah agregátu:
 * Evans:
 * - Kořen Entity má globální identitu a je zodpovědný za kontrolu invariant
 * - Kořenové Entity mají globální identitu. Entity uvnitř rozsahu mají místní identitu, unikátní pouze v rámci agregátu.
 * - Nic mimo rozsah agregátu nemůže obsahovat odkaz na něco uvnitř agragátu, s výjimkou odkazu na kořenovou entitu.
 *    Kořenová entita může předávat (ven) odkazy vnitřních entit na jiné objekty, ale je možné je použít pouze přechodně (v rámci jedné metody nebo bloku).
 * - Pouze kořen agregátu lze získat přímo pomocí databázových dotazů. Všechno ostatní musí být provedeno pomocí traverzování.
 * - Objekty uvnitř agregátu mohou obsahovat odkazy na kořeny jiných agregátů.
 * - Operace delete musí odebrat vše v rámci rozsahu agregátu najednou.
 * - Je-li provedena změna jakéhokoliv objektu v rámci agregátu, musí být zachovány všechny invarianty celého agregátu.
 *
 *
 * Repository se navenek tváří, jakoby všechny objekty obsahovala. Pokud jsou členské objekty agregátu persistable objekty - to obvykle ano -
 * jsou tyto objekty ve skutečnosti na pozadí načítány a ukládány do nějakého úložiště.
 *
 * Pro načítání, a ukládání dat repository vnitřně pracuje s další vrstvou modelu - pro jednotlivé členy agregátu používá nějaké DAO.
 * ???:
 * Můžu repository použít jako vnořené - to jest pro kořenovou entitu použiji její DAO (např. řádkový objekt db tabulky) a pro potomkovské členy
 * agregátu použiji jejich repository - pochopitelně, pokud má smysl, aby existovalo - a volám metodu itemRepository->find().
 *
 * Příklad:
 * Mám firmy, k firmě faktury (1:N), k faktuře položky (1:N). Protže někdy potřebuji pracovat i s položkami - například chci najít všechny položky faktur,
 * které obsahují cosi - tak už mám repository pro položku. Potom by repository pro fakturu mohlo pro přístup k datům faktury používat DAO faktury
 * (pro přístup k řádkovým objektům db tabulky faktura) a pro přístup k datům položek již existující repository pro položky.
 *
 * Repository "hovoří" jazykem domény. Například má repository pro faktury metodu vracející neuhrazené faktury findOutstanding(). To je jazyk domény,
 * DAO neznají nic takového jako je "neuhrazené".
 * @author pes2704
 */
namespace Pes\Repository;

interface RepositoryInterface extends \Countable, \IteratorAggregate, \ArrayAccess {

    public function set($entity);

    public function remove($entity);

    /**
     * @throws NotFoundException
     * @return object
     */
    public function get($id);

    /**
     * Vrací kolekci kořenů agregátů - kořen je entita.
     *
     * @return Pes\Collection\EntityCollectionInterface
     */
    public function find(CriteriaInterface $criteria);

}
