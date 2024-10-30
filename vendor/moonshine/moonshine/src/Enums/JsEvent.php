<?php

declare(strict_types=1);

namespace MoonShine\Enums;

enum JsEvent: string
{
    case FRAGMENT_UPDATED = 'fragment-updated';

    case TABLE_UPDATED = 'table-updated';

    case TABLE_REINDEX = 'table-reindex';

    case TABLE_ROW_UPDATED = 'table-row-updated';

    case CARDS_UPDATED = 'cards-updated';

    case FORM_RESET = 'form-reset';

    case FORM_SUBMIT = 'form-submit';

    case MODAL_TOGGLED = 'modal-toggled';

    case OFF_CANVAS_TOGGLED = 'offcanvas-toggled';

    case TOAST = 'toast';
}
