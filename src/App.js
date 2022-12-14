import React, { useState, useEffect } from 'react';
import logo from './logo.svg';
// import './App.css';
import Tiptap from './components/TipTap';
import { Container, AppShell, Burger, Header } from '@mantine/core';
import ButtonAddData from './components/ButtonAddData'
import { Button } from '@mantine/core';
import * as db from './includes/db'

function App() {

  const [count, setCount] = useState(0);

  useEffect(() => {
    db.set('count', count);
  });

  

  return (
    <Container>
      <Header height={60} p="xs">
        <Burger/>
        <Button onClick={() => setCount(count + 1)}>Add</Button>
        
      </Header>
      <Tiptap />
      
    </Container>
  );
}

export default App;
