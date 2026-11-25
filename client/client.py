import getpass
import re
import requests

BASE_URL = "http://127.0.0.1:8000"


def show_books():
    query = input("Пошук (або Enter для всіх): ").strip()
    params = {"q": query} if query else {}

    resp = requests.get(f"{BASE_URL}/api/books", params=params, timeout=5)
    resp.raise_for_status()
    data = resp.json()

    print(f"\nЗнайдено книг: {data['count']}")
    print("-" * 60)
    for book in data["items"]:
        author_name = book["author"]["name"] if book["author"] else "Невідомий"
        print(f"[{book['id']}] {book['title']} — {author_name} ({book['publishedYear']} р., {book['pages']} стор.)")
    print("-" * 60)


def show_book_detail():
    book_id = input("Введіть ID книги: ").strip()
    resp = requests.get(f"{BASE_URL}/api/books/{book_id}", timeout=5)

    if resp.status_code == 404:
        print("Книгу з таким ID не знайдено.")
        return

    resp.raise_for_status()
    book = resp.json()
    author = book["author"] or {}

    print("\n" + "=" * 60)
    print(f"Назва:    {book['title']}")
    print(f"Автор:    {author.get('name', 'Невідомий')} ({author.get('country') or '—'})")
    print(f"Рік:      {book['publishedYear']}")
    print(f"Сторінок: {book['pages']}")
    print(f"Опис:     {book['description'] or 'Немає опису'}")
    print("=" * 60)


def login_to_admin():
    print("\n--- Вхід в адмінку через сесію ---")
    email = input("Email: ").strip()
    password = getpass.getpass("Пароль: ")

    session = requests.Session()
    login_page = session.get(f"{BASE_URL}/login", timeout=5)
    login_page.raise_for_status()

    match = re.search(r'name="_csrf_token"\s+value="([^"]+)"', login_page.text)
    csrf_token = match.group(1) if match else ""

    payload = {
        "_username": email,
        "_password": password,
        "_csrf_token": csrf_token,
    }

    resp = session.post(f"{BASE_URL}/login", data=payload, allow_redirects=True, timeout=5)

    admin_resp = session.get(f"{BASE_URL}/admin", allow_redirects=True, timeout=5)
    if "/login" in admin_resp.url or admin_resp.status_code != 200:
        print("Помилка входу: невірний логін/пароль або немає прав ROLE_ADMIN.")
    else:
        print(f"Успішний вхід! Доступ до адмінки отримано: {admin_resp.url} (HTTP {admin_resp.status_code})")


def main():
    while True:
        print("\n=== BookLibrary Python Client ===")
        print("1. Список книг (JSON API)")
        print("2. Деталі книги за ID (JSON API)")
        print("3. Перевірити вхід в адмінку (сесія)")
        print("0. Вихід")

        choice = input("Оберіть дію: ").strip()
        try:
            if choice == "1":
                show_books()
            elif choice == "2":
                show_book_detail()
            elif choice == "3":
                login_to_admin()
            elif choice == "0":
                break
            else:
                print("Невідома команда.")
        except requests.RequestException as e:
            print(f"Помилка з'єднання із сервером: {e}")


if __name__ == "__main__":
    main()