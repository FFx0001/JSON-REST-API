<?php


namespace App\Services;


use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PaginationService
{
    private $iPage = null;
    private $iPerPage = null;
    private $allItems = [];
    private $countItems = 0;

    function __construct($allItems)
    {
        $this->allItems = $allItems;
        $this->countItems = count($allItems);
        $this->iPage = (int)request()->input('page');
        $this->iPerPage = (int)request()->input('per_page');
    }

    /**
     * Select page and per_page from query and response page chunk or all items if condition pagination service error
     * @return array
     */
    public function getCurrentPage()
    {
        if (isset($this->iPage) && isset($this->iPerPage) && $this->iPage > 0 && $this->iPerPage >0) {
            $oPaginator = new LengthAwarePaginator($this->allItems, $this->countItems, $this->iPerPage, $this->iPage);
            $iCurrentPage = $oPaginator->currentPage();
            $iPerPage = $oPaginator->perPage();
            $iLastPage = $oPaginator->lastPage();

            if ($iCurrentPage > $iLastPage) {
                throw new BadRequestHttpException('Current page number > number last page');
            }

            return  [
                'meta' => [
                    'pagination' => [
                        'current_page' => $iCurrentPage,
                        'per_page' => $iPerPage,
                        'last_page' => $iLastPage,
                        'total' => $oPaginator->total(),
                    ],
                ],
                'data' => $this->selectItemsForCurrentPage($oPaginator),
            ];
        }

        return $this->allItems;
    }

    /**
     * Select elements for current page in paginator
     * @param LengthAwarePaginator $oPaginator
     * @return array
     */
    protected function selectItemsForCurrentPage(LengthAwarePaginator $oPaginator){
        $arResult = [];
        $currentPage = $oPaginator->currentPage();
        $perPage = $oPaginator->perPage();
        $startPoint = ($currentPage * $perPage) - $perPage;
        $endPoint = $startPoint + $perPage;
        $items = $oPaginator->items();

        for ($i = $startPoint; $i < $endPoint; $i++) {
            if ($i < $oPaginator->total()) {
                $arResult[] = $items[$i];
            } else {
                break;
            }
        }

        return $arResult;
    }
}
