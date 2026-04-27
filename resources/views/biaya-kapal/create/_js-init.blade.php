    // Store pricelist buruh data
    const pricelistBuruhData = {!! json_encode($pricelistBuruh) !!};

    // Store pricelist Biaya Trucking data
    const pricelistBiayaTruckingData = {!! json_encode($pricelistBiayaTrucking) !!};

    // Store pricelist TKBM data for Biaya TKBM
    const pricelistTkbmData = {!! json_encode($pricelistTkbm ?? []) !!};

    // Store pricelist OPP/OPT data
    const pricelistOppOptData = {!! json_encode($pricelistOppOpt ?? []) !!};

    // Store pricelist Freight data
    const pricelistFreightsData = {!! json_encode($pricelistFreights ?? []) !!};
    const pricelistFreightVendorsData = {!! json_encode($pricelistFreightVendors ?? []) !!};

    // Store Dokumen Perijinan Kapal data
    const dokumenPerijinansData = {!! json_encode($dokumenPerijinans ?? []) !!};

    // Global Kapals data used across many sections
    const allKapalsData = {!! json_encode($kapals) !!};

    // Store pricelist perijinans data
    const pricelistPerijinansData = {!! json_encode($pricelistPerijinans ?? []) !!};

    // Store pricelist meratus data
    const pricelistMeratusData = {!! json_encode($pricelistMeratus ?? []) !!};

    // Store pricelist temas data
    const pricelistTemasData = {!! json_encode($pricelistTemas ?? []) !!};
