// WooCommerce API configuration
const API_URL = import.meta.env.VITE_WOOCOMMERCE_URL;
const CONSUMER_KEY = import.meta.env.VITE_WOOCOMMERCE_CONSUMER_KEY;
const CONSUMER_SECRET = import.meta.env.VITE_WOOCOMMERCE_CONSUMER_SECRET;
const API_VERSION = 'wc/v3';

// Helper function to create the API URL with authentication
const createApiUrl = (endpoint: string, params: Record<string, any> = {}) => {
  if (!API_URL) {
    throw new Error('WooCommerce API URL is not defined');
  }
  
  // Create the base URL
  const url = new URL(`${API_URL}/wp-json/${API_VERSION}/${endpoint}`);
  
  // Add authentication parameters
  url.searchParams.append('consumer_key', CONSUMER_KEY);
  url.searchParams.append('consumer_secret', CONSUMER_SECRET);
  
  // Add any additional parameters
  Object.entries(params).forEach(([key, value]) => {
    url.searchParams.append(key, String(value));
  });
  
  return url.toString();
};

// Types for WooCommerce products
export interface Product {
  id: number;
  name: string;
  description: string;
  price: string;
  regular_price: string;
  sale_price: string;
  images: Array<{
    id: number;
    src: string;
    alt: string;
  }>;
  permalink: string;
  stock_status: string;
}



// Function to get all products
export const getProducts = async (): Promise<Product[]> => {
  try {
    const url = createApiUrl('products', { per_page: 20 });
    const response = await fetch(url);
    
    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }
    
    return await response.json();
  } catch (error: any) {
    throw error;
  }
};

// Function to get a single product by ID
export const getProductById = async (id: number): Promise<Product> => {
  try {
    const url = createApiUrl(`products/${id}`);
    const response = await fetch(url);
    
    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }
    
    return await response.json();
  } catch (error: any) {
    throw error;
  }
};






