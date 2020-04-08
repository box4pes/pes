<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Http\Cookies;

use Pes\Type\Enum;

/**
 * Description of SameSiteEnum
 *
 * Possible values for the flag are lax or strict. Support was added to Chrome 51.
 * - The strict value will prevent the cookie from being sent by the browser to the target site in all cross-site browsing context, even when following
 *  a regular link.
 * - The lax value will only send cookies for TOP LEVEL navigation GET requests. This is sufficient for user tracking, but it will prevent many
 *  CSRF attacks.
 *
 * @author pes2704
 */
class SameSiteEnum  extends Enum {
    const STRICT = 'strict';
    const LAX = 'lax';
}
