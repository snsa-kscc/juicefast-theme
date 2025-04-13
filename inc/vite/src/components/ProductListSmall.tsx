import { useState, useEffect } from "react";
import { getProducts, Product } from "@/services/woocommerce";

// For debugging
const API_URL = import.meta.env.VITE_WOOCOMMERCE_URL;
const API_KEY = import.meta.env.VITE_WOOCOMMERCE_CONSUMER_KEY;
const API_SECRET = import.meta.env.VITE_WOOCOMMERCE_CONSUMER_SECRET;

const ProductListSmall = () => {
  const [products, setProducts] = useState<Product[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchProducts = async () => {
      try {
        setLoading(true);
        console.log("Fetching products with config:", {
          url: API_URL,
          hasKey: !!API_KEY,
          hasSecret: !!API_SECRET,
        });

        const data = await getProducts();
        console.log("Products data received:", data);
        // Only take the first 3 products
        setProducts(data.slice(0, 3));
        setError(null);
      } catch (err: any) {
        const errorMessage = err?.message || "Unknown error";
        setError(`Failed to fetch products: ${errorMessage}`);
        console.error("Error details:", err);
      } finally {
        setLoading(false);
      }
    };

    fetchProducts();
  }, []);

  if (loading) {
    return <div className="flex justify-center items-center p-8 text-lg">Loading products...</div>;
  }

  if (error) {
    return (
      <div className="bg-red-50 border border-red-200 rounded-lg p-6 my-4">
        <h3 className="text-xl font-bold text-red-700 mb-2">Error</h3>
        <p className="text-red-600 mb-4">{error}</p>
        <div className="bg-gray-50 p-4 rounded border border-gray-200">
          <h4 className="font-medium mb-2">Debug Information</h4>
          <p className="text-sm text-gray-700">API URL: {API_URL || "Not set"}</p>
          <p className="text-sm text-gray-700">API Key: {API_KEY ? "Set (hidden)" : "Not set"}</p>
          <p className="text-sm text-gray-700">API Secret: {API_SECRET ? "Set (hidden)" : "Not set"}</p>
        </div>
      </div>
    );
  }

  if (products.length === 0) {
    return <div className="text-center p-8 text-gray-500">No products found.</div>;
  }

  return (
    <div className="w-full">
      <h2 className="text-2xl font-bold mb-6">Featured Products (Small) dev</h2>
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
        {products.map((product) => (
          <div key={product.id} className="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
            {product.images && product.images.length > 0 ? (
              <img src={product.images[0].src} alt={product.images[0].alt || product.name} className="w-full h-48 object-cover" />
            ) : (
              <div className="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-400">No image available</div>
            )}
            <div className="p-4">
              <h3 className="font-medium text-lg mb-2">{product.name}</h3>
              <div className="mb-2">
                {product.sale_price ? (
                  <div className="flex items-center gap-2">
                    <span className="text-gray-500 line-through">${product.regular_price}</span>
                    <span className="text-green-600 font-bold">${product.sale_price}</span>
                  </div>
                ) : (
                  <span className="font-bold">${product.price}</span>
                )}
              </div>
              <a
                href={product.permalink}
                target="_blank"
                rel="noopener noreferrer"
                className="inline-block bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded text-sm transition-colors duration-300"
              >
                View Details
              </a>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};

export default ProductListSmall;
