import { render, screen } from '@testing-library/react';
import App from './App';

test('renders ForgeCMS link', () => {
  render(<App />);
  const linkElement = screen.getByText(/ForgeCMS/i);
  expect(linkElement).toBeInTheDocument();
});
