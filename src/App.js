import React from 'react';
import logo from './logo.svg';
// import './App.css';
import Tiptap from './components/TipTap';
import { Container, AppShell, Burger, Header } from '@mantine/core';


function App() {
  return (
    <Container>
      <Header height={60} p="xs">
        <Burger/>
      </Header>
      <Tiptap />
      
    </Container>
  );
}

export default App;
