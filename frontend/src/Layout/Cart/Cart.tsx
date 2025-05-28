// src/Layout/Cart/Cart.tsx
import React from 'react';
import CartComponent from "../../Component/CartComponent/CartComponent";

const Cart: React.FC = () => {
  const userId = 4; 

  return (
    <>
      <main className="main">
        <CartComponent userId={userId} />
      </main>
    </>
  );
};

export default Cart;
