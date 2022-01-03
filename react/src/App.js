// import logo from './logo.svg';
// import './App.css';

// function App() {
//   return (
//     <div className="App">
//       <header className="App-header">
//         <img src={logo} className="App-logo" alt="logo" />
//         <p>
//           Edit <code>src/App.js</code> and save to reload.
//         </p>
//         <a
//           className="App-link"
//           href="https://reactjs.org"
//           target="_blank"
//           rel="noopener noreferrer"
//         >
//           Learn React
//         </a>
//       </header>
//     </div>
//   );
// }

// export default App;

// 初期テスト（コンポーネントなし）
// import React from 'react'

// const App = (props) => {
//   return (
//     <div>
//       <h1>Hello Hello Hello</h1>
//     </div>
//   )
// }

// export default App

// コンポーネント
import React, {Component} from 'react'

class App extends Component {

  constructor(props) {
    super(props);
    this.state = {name: 'DOG'};
  }
  
  handleClick(name) {
    this.setState({name:name});
  }
  
  render() {
    return (
      <div>
        <h1>Hello, {this.state.name}!</h1>
        <p>click</p>
        <button onClick={() => {this.handleClick('DOG')}}>犬</button>
        <button onClick={() => {this.handleClick('Human')}}>人</button>
      </div>
    );
  }

}

export default App