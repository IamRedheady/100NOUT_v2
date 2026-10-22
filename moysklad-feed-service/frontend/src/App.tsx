import { BrowserRouter, Navigate, Route, Routes } from "react-router-dom";
import { AppShell } from "./layout/AppShell";
import { FeedFormPage } from "./pages/FeedFormPage";
import { FeedsListPage } from "./pages/FeedsListPage";
import { JournalPage } from "./pages/JournalPage";
import { ProductsPage } from "./pages/ProductsPage";
import { SettingsPage } from "./pages/SettingsPage";

export default function App() {
  return (
    <BrowserRouter>
      <AppShell>
        <Routes>
          <Route path="/" element={<Navigate to="/settings" replace />} />
          <Route path="/settings" element={<SettingsPage />} />
          <Route path="/feeds" element={<FeedsListPage />} />
          <Route path="/feeds/new" element={<FeedFormPage />} />
          <Route path="/feeds/:id" element={<FeedFormPage />} />
          <Route path="/products" element={<ProductsPage />} />
          <Route path="/journal" element={<JournalPage />} />
        </Routes>
      </AppShell>
    </BrowserRouter>
  );
}
