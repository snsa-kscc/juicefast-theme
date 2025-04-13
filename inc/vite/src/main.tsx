import React from "react";
import ReactDOM from "react-dom/client";
import "./index.css";
import ProductList from "./components/ProductList";
import ProductListSmall from "./components/ProductListSmall";

const mountComponent = (containerId: string, Component: React.FC) => {
  const container = document.getElementById(containerId);
  if (container) {
    ReactDOM.createRoot(container).render(
      <React.StrictMode>
        <Component />
      </React.StrictMode>
    );
  }
};

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", () => {
    mountComponent("react-island-products", ProductList);
    mountComponent("react-island-products-small", ProductListSmall);
  });
} else {
  mountComponent("react-island-products", ProductList);
  mountComponent("react-island-products-small", ProductListSmall);
}
