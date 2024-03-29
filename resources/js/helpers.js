const formatRupiah = number => {
    if (number === null) {
        number = 0;
    }

    let number_string = number.toString(),
        sisa = number_string.length % 3,
        rupiah = number_string.substr(0, sisa),
        ribuan = number_string.substr(sisa).match(/\d{3}/g);

    if (ribuan) {
        const separator = sisa ? "." : "";
        rupiah += separator + ribuan.join(".");
    }
    return `Rp ${rupiah}`;
};

export { formatRupiah };
