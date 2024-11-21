import {test, expect} from './BaseTestFile';

const PRODUCT_UUID = "b7d2554b0ce847cd82f3ac9bd1c0dfca";

async function fetchDefaultTax(AdminApiContext: any) {
    const taxResponse = await AdminApiContext.get('./tax');
    const {data: taxData} = await taxResponse.json();
    return taxData[0] ? taxData[0].id : null;
}

async function fetchStorefrontSalesChannel(AdminApiContext: any) {
    const salesChannelResponse = await AdminApiContext.get('./sales-channel');
    const {data: salesChannelData} = await salesChannelResponse.json();
    const storefrontSalesChannel = salesChannelData.find((channel: any) => channel.typeId === '8a243080f92e4c719546314b577cf82b');
    return storefrontSalesChannel ? storefrontSalesChannel.id : null;
}

async function deleteProduct(AdminApiContext: any, productId: string) {
    await AdminApiContext.delete(`./product/${productId}`);
}

test('Plugin test scenario.', async ({AdminApiContext, page}) => {
    const firstTaxId = await fetchDefaultTax(AdminApiContext);
    expect(firstTaxId).not.toBeNull();

    const storefrontSalesChannelId = await fetchStorefrontSalesChannel(AdminApiContext);
    expect(storefrontSalesChannelId).not.toBeNull();

    const productResponse = await AdminApiContext.post('./product', {
        data: {
            id: PRODUCT_UUID,
            name: "New Academy Playwright Product",
            productNumber: "ACADEMY-DEMO-001",
            stock: 100,
            price: [{
                currencyId: PRODUCT_UUID,
                gross: 19.99,
                net: 16.80,
                linked: false
            }],
            taxId: firstTaxId,
            active: true,
            visibilities: [{
                salesChannelId: storefrontSalesChannelId,
                visibility: 30
            }]
        }
    });

    expect(productResponse.status()).toBe(204);

    await page.goto('http://production.local/detail/c7bca22753c84d08b6178a50052b4146');

    const openImageModalLink = page.locator('a.open-image-modal');
    await expect(openImageModalLink).toHaveCount(1);

    await deleteProduct(AdminApiContext, PRODUCT_UUID);
});