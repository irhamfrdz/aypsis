    // Store pricelist buruh data
    const pricelistBuruhData = {!! json_encode($pricelistBuruh) !!};

    // Store pricelist TKBM data for Biaya TKBM
    const pricelistTkbmData = {!! json_encode($pricelistTkbm ?? []) !!};

    // Store pricelist OPP/OPT data
    const pricelistOppOptData = {!! json_encode($pricelistOppOpt ?? []) !!};
