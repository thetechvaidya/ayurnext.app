#!/usr/bin/env python3
import requests
import json

BASE_URL = "http://localhost:8000/api/v1"

def test_register():
    """Test user registration"""
    print("Testing user registration...")
    url = f"{BASE_URL}/auth/register"
    data = {
        "name": "John Doe",
        "email": "john@example.com",
        "password": "password123",
        "password_confirmation": "password123",
        "phone_number": "+1234567890"
    }
    
    try:
        response = requests.post(url, json=data)
        print(f"Status: {response.status_code}")
        print(f"Response: {json.dumps(response.json(), indent=2)}")
        return response.json().get('data', {}).get('token')
    except Exception as e:
        print(f"Error: {e}")
        return None

def test_login():
    """Test user login"""
    print("\nTesting user login...")
    url = f"{BASE_URL}/auth/login"
    data = {
        "email": "test@example.com",
        "password": "password"
    }
    
    try:
        response = requests.post(url, json=data)
        print(f"Status: {response.status_code}")
        print(f"Response: {json.dumps(response.json(), indent=2)}")
        return response.json().get('data', {}).get('token')
    except Exception as e:
        print(f"Error: {e}")
        return None

def test_profile(token):
    """Test getting user profile"""
    if not token:
        print("No token available, skipping profile test")
        return
        
    print(f"\nTesting user profile...")
    url = f"{BASE_URL}/user/profile"
    headers = {"Authorization": f"Bearer {token}"}
    
    try:
        response = requests.get(url, headers=headers)
        print(f"Status: {response.status_code}")
        print(f"Response: {json.dumps(response.json(), indent=2)}")
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    print("=== Ayurvedic Exam API Testing ===")
    
    # Test registration with new user
    token = test_register()
    
    # Test profile with new token
    test_profile(token)
    
    # Test login with seeded user
    token = test_login()
    test_profile(token)